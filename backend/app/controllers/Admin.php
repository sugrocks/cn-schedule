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

    // tempfix: For some reason 2018-01-01 is in the wrong order
    $sh = json_decode(json_encode($json->schedule), true);
    usort($sh, function ($item1, $item2) {
      return $item1['timestamp'] <=> $item2['timestamp'];
    });

    // Add from the correct source
    if ($json->source == 'Cartoon Network') {
      $day->cn = json_encode($sh);
    } else if ($json->source == 'Zap2it') {
      $day->zap = json_encode($sh);
    }  else if ($json->source == 'Adult Swim') {
      $day->as = json_encode($sh);
    } else {
      $day->other = json_encode($sh);
    }

    // Save
    $day->save();
  }

  private function saveStats($f3, $json) {
    $stats = $f3->get('stats');
    $count = [];
    $total = 0;

    // Count how many times a show airs in the day
    foreach ($json->schedule as $block) {
      if (array_key_exists($block->show, $count)) {
        $count[$block->show] += $block->slots;
      } else {
        $count[$block->show] = $block->slots;
      }

      $total += $block->slots;
    }

    // Now save that with percentage
    $res = [];
    foreach ($count as $title => $slots) {
      $res[] = array(
        'title' => $title,
        'slots' => $slots,
        'percentage' => floor($slots / $total * 100)
      );
    }

    // Sort DESC by slots
    usort($res, function ($item1, $item2) {
        return $item2['slots'] <=> $item1['slots'];
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
    if ($json->source == 'Cartoon Network') {
      $stats->cn = json_encode($res);
    } else if ($json->source == 'Zap2it') {
      $stats->zap = json_encode($res);
    }  else if ($json->source == 'Adult Swim') {
      $stats->as = json_encode($res);
    } else {
      $stats->other = json_encode($res);
    }

    // Save
    $stats->save();
  }

  public function submit($f3) {
    // Check if key is valid
    $keys = $f3->get('keys');
    $keys->load(
      array('value=?', $f3->get('HEADERS.X-Api-Key'))
    );
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
