<?php

namespace core\common;

use Exception;
use Symfony\Component\Yaml\Yaml;

class ConfigReader
{

  private $configFileName;
  private $config;

  public function __construct($configName)
  {
    $this->configFileName = $configName;
  }

  /**
   * @return mixed
   * @throws Exception
   */
  private function getConfig()
  {

    if (empty($this->config)) {
      $configContainer = ConfigContainer::getInstance();
      $config = $configContainer->getConfig($this->configFileName);
      if(empty($config)) {
        $filePath = __DIR__ . '/../../config/' . $this->configFileName . '.config.yml';
        if (!file_exists($filePath)) {
          throw new Exception('Config \'' . $this->configFileName . '\' could not be found.');
        }
        $config = Yaml::parse(file_get_contents($filePath), FILE_USE_INCLUDE_PATH);
        $configContainer->setConfigInstance($this->configFileName, $config);
      }
      $this->config = $config;
    }
    return $this->config;

  }

  /**
   * @param $key
   * @return string|array
   * @throws Exception
   */
  public function requireConfig($key)
  {

    $keys = $key;
    if (!is_array($key)) {
      $keys = [$key];
    }

    $config = $this->getConfig();

    $keyValues = [];
    $missing = [];
    foreach ($keys as $keyItem) {
      if (!array_key_exists($keyItem, $config) || $config[$keyItem] == '') {
        $missing[] = '\'' . $keyItem . '\'';
      } else {
        $keyValues[$keyItem] = $config[$keyItem];
      }
    }

    if (!empty($missing)) {
      throw new Exception('Config \'' . $this->configFileName . '\' file does not have ' . implode(', ', $missing) . ' defined.');
    } else {
      if (!is_array($key)) {
        return $config[$key];
      } else {
        return $keyValues;
      }
    }

  }

}