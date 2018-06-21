<?php
class Admin {
  public function beforeRoute($f3) {
    $db = $f3->get('db');
    $f3->set('keys', new DB\SQL\Mapper($db, 'keys'));
    $f3->set('days', new DB\SQL\Mapper($db, 'days'));
    $f3->set('stats', new DB\SQL\Mapper($db, 'stats'));
  }

  public function home($f3) {
    // Redirect to API homepage
    $f3->reroute('https://api.ctoon.network/');
  }

  private function encodeSource($obj, $json, $res) {
    if ($json->source == 'Cartoon Network') {
      $obj->cn = json_encode($res);
    } else if ($json->source == 'Zap2it') {
      $obj->zap = checkIncompleteZap(
        json_encode($res)
      );
    } else if ($json->source == 'TVGuide') {
      $obj->tvguide = json_encode($res);
    }  else if ($json->source == 'Adult Swim') {
      $obj->as = json_encode($res);
    } else {
      $obj->other = json_encode($res);
    }

    return $obj;
  }

  private function checkIncompleteZap($day) {
    // Get latest schedule entry for the day
    $l = array_values(array_slice($day, -1))[0];

    // Get its ending hour
    $ts = $l->timestamp_end;
    $tz = new DateTimeZone('America/New_York');
    $d = new DateTime("@$ts");
    $d->setTimezone($tz);

    // If it doesn't end at 8pm (20h), data is complete
    if ($d->format('H') != '20') {
      // Empty schedule and stats from Zap2it
      $day = null;
    }

    return $day;
  }

  private function saveDay($f3, $json) {
    $day = $f3->get('days');

    // Fetch if we already have that date
    $day->load(
      array('date=?', $json->date)
    );

    // If it doesn't exists, create it
    if ($day->dry()) {
      $day->reset();
    }

    // Set values
    $day->date = $json->date;
    $day->lastupdate = time();

    // Make sure everything is in the correct order based on timestamp
    $sh = json_decode(json_encode($json->schedule), true);
    usort($sh, function($item1, $item2) {
      return $item1['timestamp'] <=> $item2['timestamp'];
    });

    // Encode our JSONs
    $day = $this->encodeSource($day, $json, $sh);

    if (!$day->cn && !$day->zap && !$day->tvguide && !$day->as) {
      // Empty day?
      return;
    }

    // Save
    $day->save();
  }

  private function saveStats($f3, $json) {
    $stats = $f3->get('stats');
    $colors = [];
    $minutes = [];
    $slots = [];
    $totalMin = 0;
    $totalSlots = 0;

    // Count how many times a show airs in the day
    foreach ($json->schedule as $block) {
      $min = ($block->timestamp_end - $block->timestamp) / 60;

      if (array_key_exists($block->show, $minutes)) {
        $slots[$block->show] += $block->slots;
        $minutes[$block->show] += $min;
      } else {
        $colors[$block->show] = $block->colors->background;
        $slots[$block->show] = $block->slots;
        $minutes[$block->show] = $min;
      }

      $totalMin += $min;
      $totalSlots += $block->slots;
    }

    // Now save that with percentage
    $res = [];
    foreach ($minutes as $title => $min) {
      $res[] = array(
        'title' => (string)$title,
        'color' => (string)$colors[$title],
        'minutes' => (int)$min,
        'slots' => (int)$slots[$title],
        'percentage' => (float)sprintf('%1.1f', ($min / $totalMin * 100)) // precision: 1 decimal
      );
    }

    // Sort DESC by minutes
    usort($res, function($item1, $item2) {
      return $item2['minutes'] <=> $item1['minutes'];
    });

    // Fetch if we already have that date
    $stats->load(
      array('date=?', $json->date)
    );

    // If it doesn't exists, create it
    if ($stats->dry()) {
      $stats->reset();
    }

    // Set values
    $stats->date = $json->date;
    $stats->lastupdate = time();

    // Add from the correct source
    $stats = $this->encodeSource($stats, $json, $res);

    // Save
    $stats->save();
  }

  public function submit($f3) {
    // Check if key is valid
    $keys = $f3->get('keys');
    $keys->load(
      array('value=?', $f3->get('HEADERS.X-Api-Key'))
    );

    // If key not found, drop a 403
    if ($keys->dry()) {
      $f3->error(403);
      return;
    }

    // Read body (json) input
    $json = json_decode($f3->get('BODY'));

    // Check if schedule is not empty
    if (count($json->schedule) > 0) {
      $this->saveDay($f3, $json);
      $this->saveStats($f3, $json);

      echo json_encode(array(
        'result' => 'Saved!'
      ));
    } else {
      $f3->error('412', 'Empty schedule, ignored.');
    }
  }
}
