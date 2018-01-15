<?php

require_once __DIR__ . '/../../autoload.php';

use core\database\DatabaseController;
use TinfoilHMAC\API\SecureRequest;
use core\common\ConfigReader;

$configReader = new ConfigReader('chub');

$databaseController = new DatabaseController();

$event = $databaseController->getAllEvents()[0];

$recordings = $event['Recordings'];

$formattedRecordings = [];

foreach($recordings as $recording) {
  $path = $recording['File_Location'];

  $files = glob($path . '/*.jpg');

  foreach($files as $file) {
    $type = pathinfo($file, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $formattedRecordings[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
  }

}

$chubId = $configReader->requireConfig('chubId');

$request = new SecureRequest('POST', $chubId, 'alarmTrigger', [
  'triggerName' => 'Trigger #',
  'snapshots' => $formattedRecordings,
]);
$response = $request->send();