<?php

namespace core\common;

class PythonExecuter
{

  const SCRIPT_BASE_LOCATION = '/Users/martijnbots/Documents/CHUB/CHUB/';

  public static function exec($scriptName, $args) {
    $command = '/usr/bin/python ' . self::SCRIPT_BASE_LOCATION . $scriptName . ' ' . $args . ' 2>&1';
    $output = exec($command);
    $output = preg_replace("/u'([\/ A-Za-z0-9:\._\-]*)'/", "\"$1\"", $output);
    $output = preg_replace_callback('/(True|False)/', function ($pattern) {
      return strtolower($pattern[0]);
    }, $output);
    return $output;
  }

  public static function callJSONSerializer($args) {
    return json_decode(self::callSerializer($args), TRUE);
  }

  public static function callSerializer($args) {
    return self::exec('json_serializer.py', $args);
  }

}