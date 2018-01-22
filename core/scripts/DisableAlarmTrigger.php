<?php

require_once  __DIR__ . '/../../autoload.php';

use core\common\ConfigReader;
use TinfoilHMAC\API\SecureRequest;
use TinfoilHMAC\Util\Session;


$configReader = new ConfigReader('chub');

$chubId = $configReader->requireConfig('chubId');

Session::getInstance()->initClientSharedKey();
$request = new SecureRequest('POST', $chubId, 'alarmStatusUpdate', [
  'status' => 'enable',
  'sendNotification' => FALSE,
]);

$request->send(TRUE);