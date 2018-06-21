<?php
class API1 {
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

  private function decodeEntry($entry) {
    $entry['cn'] = json_decode($entry['cn']);
    $entry['zap'] = json_decode($entry['zap']);
    $entry['tvguide'] = json_decode($entry['tvguide']);
    $entry['as'] = json_decode($entry['as']);
    unset($entry['id']);

    # Remove incomplete zap entries
    if ($entry['zap']) {
      $l = array_values(array_slice($entry['zap'], -1))[0];

      # timestamp_end isn't a thing with stats
      if (property_exists($l, 'timestamp_end')) {
        $ts = $l->timestamp_end;
        $tz = new DateTimeZone('America/New_York');
        $d = new DateTime("@$ts");
        $d->setTimezone($tz);
        if ($d->format('H') != '20') $entry['zap'] = null;
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
    $f3->get('days')->load(
      array('date=?', $f3->get('PARAMS.date'))
    );

    if ($f3->get('days')->dry()) {
      $f3->error(404, 'There isn\'t any data available about that day.');
      return;
    }

    $entry = $this->decodeEntry($f3->get('days')->cast());

    echo json_encode($entry, JSON_PRETTY_PRINT);
  }

  public function dayRange($f3) {
    $output = [];

    $f3->get('days')->load(
      array('date >= ? AND date <= ?', $f3->get('PARAMS.start'), $f3->get('PARAMS.end')),
      array(
        'order' => 'date ASC'
      )
    );

    if ($f3->get('days')->dry()) {
      $f3->error(404, 'There isn\'t any data available in that range.');
      return;
    }

    while(!$f3->get('days')->dry()) {
      $entry = $f3->get('days')->cast();
      $output[$entry['date']] = $this->decodeEntry($entry);
      $f3->get('days')->next();
    }

    echo json_encode($output, JSON_PRETTY_PRINT);
  }

  public function stats($f3) {
    $f3->get('stats')->load(
      array('date=?', $f3->get('PARAMS.date'))
    );

    if ($f3->get('stats')->dry()) {
      $f3->error(404, 'There isn\'t any data available about that day.');
      return;
    }

    $entry = $this->decodeEntry($f3->get('days')->cast());

    echo json_encode($entry, JSON_PRETTY_PRINT);
  }

  public function statsRange($f3) {
    $output = [];

    $f3->get('stats')->load(
      array('date >= ? AND date <= ?', $f3->get('PARAMS.start'), $f3->get('PARAMS.end')),
      array(
        'order' => 'date ASC'
      )
    );

    if ($f3->get('stats')->dry()) {
      $f3->error(404, 'There isn\'t any data available in that range.');
      return;
    }

    while(!$f3->get('stats')->dry()) {
      $entry = $f3->get('stats')->cast();
      $output[$entry['date']] = $this->decodeEntry($entry);
      $f3->get('stats')->next();
    }

    // Create a "total" from the range
    $count_cn = [];
    $count_zap = [];
    $count_tvguide = [];
    $count_as = [];
    $total_cn = 0;
    $total_zap = 0;
    $total_tvguide = 0;
    $total_as = 0;

    // Count how many times a show airs in the day
    foreach ($output as $day => $data) {
      // CN
      if ($data['cn']) {
        foreach ($data['cn'] as $show) {
          if (array_key_exists($show->title, $count_cn)) {
            $count_cn[$show->title] += $show->slots;
          } else {
            $count_cn[$show->title] = $show->slots;
          }
          $total_cn += $show->slots;
        }
      }

      // Zap2it
      if ($data['zap']) {
        foreach ($data['zap'] as $show) {
          if (array_key_exists($show->title, $count_zap)) {
            $count_zap[$show->title] += $show->slots;
          } else {
            $count_zap[$show->title] = $show->slots;
          }
          $total_zap += $show->slots;
        }
      }

      // TVGuide
      if ($data['tvguide']) {
        foreach ($data['tvguide'] as $show) {
          if (array_key_exists($show->title, $count_tvguide)) {
            $count_tvguide[$show->title] += $show->slots;
          } else {
            $count_tvguide[$show->title] = $show->slots;
          }
          $total_tvguide += $show->slots;
        }
      }

      // [as]
      if ($data['as']) {
        foreach ($data['as'] as $show) {
          if (array_key_exists($show->title, $count_as)) {
            $count_as[$show->title] += $show->slots;
          } else {
            $count_as[$show->title] = $show->slots;
          }
          $total_as += $show->slots;
        }
      }
    }

    // Now save that with percentage
    // CN
    $res_cn = [];
    foreach ($count_cn as $title => $slots) {
      $res_cn[] = array(
        'title' => $title,
        'slots' => $slots,
        'percentage' => floor($slots / $total_cn * 100)
      );
    }
    usort($res_cn, function($item1, $item2) {
      return $item2['slots'] <=> $item1['slots'];
    });
    if (!$res_cn) $res_cn = null;

    // Zap2it
    $res_zap = [];
    foreach ($count_zap as $title => $slots) {
      $res_zap[] = array(
        'title' => $title,
        'slots' => $slots,
        'percentage' => floor($slots / $total_zap * 100)
      );
    }
    usort($res_zap, function($item1, $item2) {
      return $item2['slots'] <=> $item1['slots'];
    });
    if (!$res_zap) $res_zap = null;

    // TVGuide
    $res_tvguide = [];
    foreach ($count_tvguide as $title => $slots) {
      $res_tvguide[] = array(
        'title' => $title,
        'slots' => $slots,
        'percentage' => floor($slots / $total_tvguide * 100)
      );
    }
    usort($res_tvguide, function($item1, $item2) {
      return $item2['slots'] <=> $item1['slots'];
    });
    if (!$res_tvguide) $res_tvguide = null;

    // [as]
    $res_as = [];
    foreach ($count_as as $title => $slots) {
      $res_as[] = array(
        'title' => $title,
        'slots' => $slots,
        'percentage' => floor($slots / $total_as * 100)
      );
    }
    usort($res_as, function($item1, $item2) {
      return $item2['slots'] <=> $item1['slots'];
    });
    if (!$res_as) $res_as = null;

    $output['total'] = array(
      'cn' => $res_cn,
      'zap' => $res_zap,
      'tvguide' => $res_tvguide,
      'as' => $res_as,
    );

    echo json_encode($output, JSON_PRETTY_PRINT);
  }
}
