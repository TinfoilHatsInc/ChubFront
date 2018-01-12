<?php

require_once __DIR__ . '/../../autoload.php';

use TinfoilHMAC\API\SecureRequest;
use core\common\ConfigReader;

$configReader = new ConfigReader('chub');

$request = new SecureRequest('POST', $configReader->requireConfig('chubId'), 'sendNotification', [
  'message' => 'The alarm was triggered.',
  'subject' => 'Alarm triggered'
]);
$request->send();