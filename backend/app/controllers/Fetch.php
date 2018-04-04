<?php
class Fetch {
  // WIP: NOT USABLE AT ALL

  private function fixShowName($show) {
    switch ($show) {
      case 'MOVIE:':
        return 'MOVIE';
        break;

      case 'SPECIAL:':
        return 'SPECIAL';
        break;

      case 'Cloudy':
      case 'Cloudy With a Chance of Meatballs':
        return 'Cloudy with a Chance of Meatballs';
        break;

      case 'The Amazing World of Gumball':
      case 'Gumball':
        return 'The Amazing World of Gumball';
        break;

      case 'OK K.O.! Let\'s Be Heroes!':
      case 'OK K.O.!':
        return 'OK K.O.! Let\'s Be Heroes';
        break;

      case 'Unikitty!':
        return 'Unikitty';
        break;

      default:
        return $show;
    }
  }

  private function fisEpisodeName($episode) {
    $ep = explode('/', $episode);

    foreach ($ep as $e) {
      // ...
    }

    return $episode;
  }

  public function cn($f3) {
    // ...
  }

  public function zap($f3) {
    // ...
  }

  public function tvguide($f3) {
    // ...
  }

  public function as($f3) {
    // ...
  }
}
