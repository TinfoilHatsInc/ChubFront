<?php

namespace core\common;

class Singleton
{

  private static $instances = array();

  protected function __construct() {

  }

  /**
   * @return $this
   */
  public static function getInstance() {
    $class = get_called_class();
    if (!isset(self::$instances[$class])) {
      self::$instances[$class] = new $class();
    }
    return self::$instances[$class];
  }

}