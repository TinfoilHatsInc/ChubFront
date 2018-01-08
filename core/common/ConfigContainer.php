<?php

namespace core\common;

use core\common\Singleton;
use core\exception\ResourceNotFoundException;

class ConfigContainer extends Singleton
{

  private $configInstances;

  public function __construct()
  {
    $this->configInstances = [];
  }

  public function getConfig($name) {
    if(array_key_exists($name, $this->configInstances)) {
      return $this->configInstances[$name];
    } else {
      return [];
    }
  }

  public function setConfigInstance($name, array $config) {
    $this->configInstances[$name] = $config;
  }

}