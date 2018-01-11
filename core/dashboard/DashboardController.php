<?php

namespace core\dashboard;

use core\alarm\AlarmController;
use core\common\PythonExecuter;

class DashboardController
{

  public function build()
  {
    $config = AlarmController::getAlarmCache();
    $delayed = FALSE;
    $remaining = NULL;
    $status = PythonExecuter::callSerializer('--get_armed');
    if (!empty($config['alarmOnTimestamp'])) {
      $timestamp = new \DateTime($config['alarmOnTimestamp']);
      $diff = (new \DateTime())->diff($timestamp);
      if ($diff->y == 0
        && $diff->m == 0
        && $diff->d == 0
        && $diff->h == 0
        && $diff->i == 0
        && $diff->s < 30
      ) {
        $remaining = 30 - $diff->s;
        $delayed = TRUE;
      }
    }
    return [
      'alarmEnabled' => $status == 'true',
      'delayed' => $delayed,
      'remaining' => $remaining,
    ];
  }

}