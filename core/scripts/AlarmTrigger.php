<?php

require_once __DIR__ . '/../../autoload.php';

use core\database\DatabaseController;
use TinfoilHMAC\API\SecureRequest;
use core\common\ConfigReader;
use TinfoilHMAC\Util\Session;

$configReader = new ConfigReader('chub');

$databaseController = new DatabaseController();

$event = $databaseController->getAllEvents()[0];

$recordings = $event['Recordings'];

$formattedRecordings = [];

$maxSnapshots = $configReader->requireConfig('maxSnapshots');

foreach($recordings as $recording) {
  $path = $recording['File_Location'];

  $files = glob($path . '/*.jpg');

  $num = 0;
  foreach($files as $file) {
    if($num == $maxSnapshots) {
      break;
    }
    $type = pathinfo($file, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $formattedRecordings[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $num++;
  }

}

$chubId = $configReader->requireConfig('chubId');

Session::getInstance()->initClientSharedKey();

$request = new SecureRequest('POST', $chubId, 'alarmTrigger', [
  'triggerName' => 'Trigger #',
  'snapshots' => $formattedRecordings,
]);
$response = $request->send(TRUE);