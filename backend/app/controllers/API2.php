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

  private function makeDay($schedule, $stats) {
    $day = array(
      'schedule' => [],
      'stats' => []
    );

    // Get selected date and the last update
    $day['date'] = $schedule['date'];
    $day['lastupdate'] = $schedule['lastupdate'];

    // Decode schedules' json
    $day['schedule']['cn'] = json_decode($schedule['cn']);
    $day['schedule']['zap'] = json_decode($schedule['zap']);
    $day['schedule']['tvguide'] = json_decode($schedule['tvguide']);
    $day['schedule']['as'] = json_decode($schedule['as']);

    // Decode stats' json
    $day['stats']['cn'] = json_decode($stats['cn']);
    $day['stats']['zap'] = json_decode($stats['zap']);
    $day['stats']['tvguide'] = json_decode($stats['tvguide']);
    $day['stats']['as'] = json_decode($stats['as']);

    return $day;
  }

  public function days($f3) {
    $output = [];

    // Load all entries
    $f3->get('days')->load(
      array(),
      array('order' => 'date DESC')
    );

    // And save the date value
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
    $output = $this->makeDay(
      $f3->get('days')->cast(),
      $f3->get('stats')->cast()
    );

    echo json_encode($output, JSON_PRETTY_PRINT);
  }

  public function range($f3) {
    $output = [];

    // Load schedule and stats for the range
    $f3->get('days')->load(
      array('date >= ? AND date <= ?', $f3->get('PARAMS.start'), $f3->get('PARAMS.end')),
      array('order' => 'date ASC')
    );
    $f3->get('stats')->load(
      array('date >= ? AND date <= ?', $f3->get('PARAMS.start'), $f3->get('PARAMS.end')),
      array('order' => 'date ASC')
    );

    // Check for a 404
    if ($f3->get('days')->dry()) {
      $f3->error(404, 'There isn\'t any data available in that range.');
      return;
    }

    // Go through all selected days
    while(!$f3->get('days')->dry()) {
      // Make our json
      $output[$f3->get('days')['date']] = $this->makeDay(
        $f3->get('days')->cast(),
        $f3->get('stats')->cast()
      );

      $f3->get('days')->next();
      $f3->get('stats')->next();
    }

    echo json_encode($output, JSON_PRETTY_PRINT);
  }
}
