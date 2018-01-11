<?php

namespace core\alarm;

use core\common\ConfigReader;
use core\common\PythonExecuter;
use core\routing\RoutingController;
use Symfony\Component\Yaml\Yaml;
use TinfoilHMAC\API\SecureRequest;

class AlarmController
{

  public function enableAlarm()
  {
    $config = self::getAlarmCache();
    $timestamp = new \DateTime($config['alarmOnTimestamp']);
    $diff = (new \DateTime())->diff($timestamp);
    $status = PythonExecuter::callSerializer('--get_armed');
    if ($status == 'false' && !($diff->y == 0
      && $diff->m == 0
      && $diff->d == 0
      && $diff->h == 0
      && $diff->i == 0
      && $diff->s < 30
    )) {
      self::writeOnTimestamp();
      exec('php -f ' . __DIR__ . '/../scripts/ArmAlarm.php > /dev/null &');
    }
    RoutingController::getInstance()->redirectRoute('home');
  }

  public function disableAlarm()
  {
    $status = PythonExecuter::callSerializer('--get_armed');
    if ($status == 'true') {
      PythonExecuter::callSerializer('--set_armed');
      $configReader = new ConfigReader('chub');
      $request = new SecureRequest('POST', $configReader->requireConfig('chubId'), 'alarmStatusUpdate', [
        'status' => 'disable',
      ]);
      $request->send();
    }
    RoutingController::getInstance()->redirectRoute('home');
  }

  public static function getAlarmCache()
  {
    return Yaml::parse(file_get_contents(__DIR__ . '/alarm.cache.yml'));
  }

  private static function writeOnTimestamp()
  {
    $alarmCache = self::getAlarmCache();
    $alarmCache['alarmOnTimestamp'] = (new \DateTime())->format('Y-m-d H:i:s');
    $newCache = Yaml::dump($alarmCache);
    file_put_contents(__DIR__ . '/alarm.cache.yml', $newCache);
  }

}