<?php

require_once __DIR__ . '/../../autoload.php';

use core\common\ConfigReader;
use core\common\PythonExecuter;
use TinfoilHMAC\API\SecureRequest;

sleep(30);

PythonExecuter::callSerializer('--set_armed');

$configReader = new ConfigReader('chub');

$request = new SecureRequest('POST', $configReader->requireConfig('chubId'), 'alarmStatusUpdate', [
  'status' => 'enable',
]);
$request->send();