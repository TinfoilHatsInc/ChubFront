<?php

require_once __DIR__ . '/../../autoload.php';

use core\common\PythonExecuter;
use TinfoilHMAC\API\SecureRequest;
use core\common\ConfigReader;
use TinfoilHMAC\Util\Session;

$rooms = PythonExecuter::callJSONSerializer('-c');

$deadModules = [];

foreach($rooms as $room) {
  if($room['ID'] == 0) {
    continue;
  }
  $modules = $room['Modules'];
  foreach($modules as $module) {
    if($module['Alive'] == 0) {
      $deadModules[] = $module['Name'];
    }
  }
}

if(!empty($deadModules)) {
  $configReader = new ConfigReader('chub');
  Session::getInstance()->initClientSharedKey();
  $request = new SecureRequest('POST', $configReader->requireConfig('chubId'), 'notifyDeadModules', [
    'deadModules' => $deadModules,
  ]);
  $request->send(TRUE);
}