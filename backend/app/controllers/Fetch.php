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
        return 'The Amazing World of Gumball';
        break;

      default:
        return $show;
    }
  }

  private function fisEpisodeName($episode) {
    $ep = explode('/', $episode);

    foreach ($ep as $e) {
      //
    }

    return $episode;
  }

  public function cn($f3) {
    // ...
  }

  public function zap($f3) {
    // ...
  }

  public function as($f3) {
    // ...
  }
}
