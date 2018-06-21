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

    $entry['date'] = $schedule['date'];
    $entry['lastupdate'] = $schedule['lastupdate'];

    $entry['stats']['cn'] = json_decode($stats['cn']);
    $entry['stats']['zap'] = json_decode($stats['zap']);
    $entry['stats']['tvguide'] = json_decode($stats['tvguide']);
    $entry['stats']['as'] = json_decode($stats['as']);

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
    if ($f3->get('days')->dry()) {
      $f3->error(404, 'There isn\'t any data available about that day.');
      return;
    }

    // Make our json
    $entry = $this->decodeEntry(
      $f3->get('days')->cast(),
      $f3->get('stats')->cast()
    );

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
    if ($f3->get('days')->dry()) {
      $f3->error(404, 'There isn\'t any data available in that range.');
      return;
    }

    // Make our json
    while(!$f3->get('days')->dry()) {
      $output[$entry['date']] = $this->decodeEntry(
        $f3->get('days')->cast(),
        $f3->get('stats')->cast()
      );

      $f3->get('days')->next();
      $f3->get('stats')->next();
    }

    echo json_encode($output, JSON_PRETTY_PRINT);
  }
}
