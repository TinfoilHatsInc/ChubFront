<?php

require_once __DIR__ . '/../../autoload.php';

use core\database\DatabaseController;

$databaseController = new DatabaseController();

$events = $databaseController->getAllEvents();

foreach ($events as $event) {

  if (!$event['Important']) {
    $date = strtotime($event['Datetime']);
    $date = strtotime('+1 month', $date);
    $now = strtotime('now');
    if ($date < $now) {
      // TODO remove event through JSONSerializer
      $recordings = $event['Recordings'];
      foreach ($recordings as $recording) {
        $location = __DIR__ . '/../..' . $recording['File_Location'];
        // Remove all files inside recording location
        array_map('unlink', glob($location . "/*.*"));
        // Remove folder
        rmdir($location);
      }
    }
  }

}