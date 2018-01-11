<?php

namespace core\playback;

use core\common\ConfigReader;
use core\common\PythonExecuter;
use core\routing\RoutingController;
use TinfoilHMAC\API\SecureRequest;
use TinfoilHMAC\Exception\InvalidResponseException;

class EventFlagController
{

  public function flagEvent() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      $eventId = $_POST['eventId'];
      $roomId = $_POST['roomId'];

      $output = PythonExecuter::callJSONSerializer('-u ' . $eventId . ',' . $roomId . ',True');

      if(empty($output)) {
        echo json_encode([]);
      } else {
        echo json_encode([
          'error' => TRUE,
          'message' => 'Something went wrong, please try again.',
        ]);
      }
      exit;
    } else {
      RoutingController::getInstance()->throwHTTP404();
    }
  }

}