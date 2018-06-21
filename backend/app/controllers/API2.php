<?php
class API2 {
  public function beforeRoute($f3) {
    $db = $f3->get('db');
    $f3->set('days', new DB\SQL\Mapper($db, 'days'));
    $f3->set('stats', new DB\SQL\Mapper($db, 'stats'));
  }

  public function error($f3) {
    $output = array('error' => array(
      'code' => $f3->get('ERROR.code'),
      'message' => $f3->get('ERROR.text')
    ));

    echo json_encode($output, JSON_PRETTY_PRINT);
  }

  private function decodeEntry($schedule, $stats) {
    $entry = array(
      'schedule' => [],
      'stats' => []
    );

    if ($stats) {
      $entry['stats']['cn'] = json_decode($stats['cn']);
      $entry['stats']['zap'] = json_decode($stats['zap']);
      $entry['stats']['tvguide'] = json_decode($stats['tvguide']);
      $entry['stats']['as'] = json_decode($stats['as']);
    }

    if ($schedule) {
      $entry['date'] = $schedule['date'];
      $entry['lastupdate'] = $schedule['lastupdate'];

      $entry['schedule']['cn'] = json_decode($schedule['cn']);
      $entry['schedule']['zap'] = json_decode($schedule['zap']);
      $entry['schedule']['tvguide'] = json_decode($schedule['tvguide']);
      $entry['schedule']['as'] = json_decode($schedule['as']);

      # Remove incomplete zap entries
      $l = array_values(array_slice($entry['schedule']['zap'], -1))[0];
      $ts = $l->timestamp_end;
      $tz = new DateTimeZone('America/New_York');
      $d = new DateTime("@$ts");
      $d->setTimezone($tz);
      if ($d->format('H') != '20') {
        $entry['schedule']['zap'] = null;
        $entry['stats']['zap'] = null;
      }
    }

    return $entry;
  }

  public function days($f3) {
    $output = [];

    $f3->get('days')->load(
      array(),
      array(
        'order' => 'date DESC'
      )
    );

    while(!$f3->get('days')->dry()) {
      $output[] = $f3->get('days')->date;
      $f3->get('days')->next();
    }

    echo json_encode($output, JSON_PRETTY_PRINT);
  }

  public function day($f3) {
    // Load schedule and stats for the day
    $f3->get('days')->load(
      array('date=?', $f3->get('PARAMS.date'))
    );
    $f3->get('stats')->load(
      array('date=?', $f3->get('PARAMS.date'))
    );

    // Check for a 404
    if ($f3->get('days')->dry() || $f3->get('stats')->dry()) {
      $f3->error(404, 'There isn\'t any data available about that day.');
      return;
    }

    // Make our json
    $entry = $this->decodeEntry($f3->get('days')->cast(), $f3->get('stats')->cast());

    echo json_encode($entry, JSON_PRETTY_PRINT);
  }

  public function range($f3) {
    $output = [];

    // Load schedule and stats for the range
    $f3->get('days')->load(
      array('date >= ? AND date <= ?', $f3->get('PARAMS.start'), $f3->get('PARAMS.end')),
      array(
        'order' => 'date ASC'
      )
    );
    $f3->get('stats')->load(
      array('date >= ? AND date <= ?', $f3->get('PARAMS.start'), $f3->get('PARAMS.end')),
      array(
        'order' => 'date ASC'
      )
    );

    // Check for a 404
    if ($f3->get('days')->dry() || $f3->get('stats')->dry()) {
      $f3->error(404, 'There isn\'t any data available in that range.');
      return;
    }

    // Make json with schedules
    while(!$f3->get('days')->dry()) {
      $entry = $f3->get('days')->cast();
      $output[$entry['date']] = $this->decodeEntry($entry, false);
      $f3->get('days')->next();
    }

    // Make json with stats
    while(!$f3->get('stats')->dry()) {
      $entry = $f3->get('stats')->cast();
      $output[$entry['date']]['stats'] = $this->decodeEntry($entry)['stats'];
      $f3->get('stats')->next();
    }

    // Create a "total" for the stats
    $count_cn = [];
    $count_zap = [];
    $count_tvguide = [];
    $count_as = [];
    $total_cn = 0;
    $total_zap = 0;
    $total_tvguide = 0;
    $total_as = 0;

    // Count how long a show airs in the range
    foreach ($output as $day => $data) {
      $date = $data['schedule'];
      // CN
      if ($data['cn']) {
        foreach ($data['cn'] as $show) {
          if (array_key_exists($show->title, $count_cn)) {
            $count_cn[$show->title] += $show->minutes;
          } else {
            $count_cn[$show->title] = $show->minutes;
          }
          $total_cn += $show->minutes;
        }
      }

      // Zap2it
      if ($data['zap']) {
        foreach ($data['zap'] as $show) {
          if (array_key_exists($show->title, $count_zap)) {
            $count_zap[$show->title] += $show->minutes;
          } else {
            $count_zap[$show->title] = $show->minutes;
          }
          $total_zap += $show->minutes;
        }
      }

      // TVGuide
      if ($data['tvguide']) {
        foreach ($data['tvguide'] as $show) {
          if (array_key_exists($show->title, $count_tvguide)) {
            $count_tvguide[$show->title] += $show->minutes;
          } else {
            $count_tvguide[$show->title] = $show->minutes;
          }
          $total_tvguide += $show->minutes;
        }
      }

      // [as]
      if ($data['as']) {
        foreach ($data['as'] as $show) {
          if (array_key_exists($show->title, $count_as)) {
            $count_as[$show->title] += $show->minutes;
          } else {
            $count_as[$show->title] = $show->minutes;
          }
          $total_as += $show->minutes;
        }
      }
    }

    // Now save that with percentage
    // CN
    $res_cn = [];
    foreach ($count_cn as $title => $minutes) {
      $res_cn[] = array(
        'title' => $title,
        'minutes' => $minutes,
        'percentage' => floor($minutes / $total_cn * 100)
      );
    }
    usort($res_cn, function($item1, $item2) {
      return $item2['minutes'] <=> $item1['minutes'];
    });
    if (!$res_cn) $res_cn = null;

    // Zap2it
    $res_zap = [];
    foreach ($count_zap as $title => $minutes) {
      $res_zap[] = array(
        'title' => $title,
        'minutes' => $minutes,
        'percentage' => floor($minutes / $total_zap * 100)
      );
    }
    usort($res_zap, function($item1, $item2) {
      return $item2['minutes'] <=> $item1['minutes'];
    });
    if (!$res_zap) $res_zap = null;

    // TVGuide
    $res_tvguide = [];
    foreach ($count_tvguide as $title => $minutes) {
      $res_tvguide[] = array(
        'title' => $title,
        'minutes' => $minutes,
        'percentage' => floor($minutes / $total_tvguide * 100)
      );
    }
    usort($res_tvguide, function($item1, $item2) {
      return $item2['minutes'] <=> $item1['minutes'];
    });
    if (!$res_tvguide) $res_tvguide = null;

    // [as]
    $res_as = [];
    foreach ($count_as as $title => $minutes) {
      $res_as[] = array(
        'title' => $title,
        'minutes' => $minutes,
        'percentage' => floor($minutes / $total_as * 100)
      );
    }
    usort($res_as, function($item1, $item2) {
      return $item2['minutes'] <=> $item1['minutes'];
    });
    if (!$res_as) $res_as = null;

    $output['totalstats'] = array(
      'cn' => $res_cn,
      'zap' => $res_zap,
      'tvguide' => $res_tvguide,
      'as' => $res_as,
    );

    echo json_encode($output, JSON_PRETTY_PRINT);
  }
}
