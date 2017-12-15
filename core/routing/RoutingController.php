<?php

namespace core\routing;

use core\common\Singleton;
use core\templating\TemplateController;
use Eden\Collection\Exception;
use Symfony\Component\Yaml\Yaml;

class RoutingController extends Singleton
{

  private $routeConfig;
  private $routeMapping;

  /**
   * @return array
   */
  private function getRouteConfig() {
    if(empty($this->routeConfig)) {
      $this->routeConfig = Yaml::parse(file_get_contents(__DIR__ . '/../../config/route.config.yml'));
    }
    return $this->routeConfig;
  }

  /**
   * @param $url
   * @return array
   */
  private function getRouteMap($url) {

    if(empty($this->routeMapping)) {
      $this->routeMapping = Yaml::parse(file_get_contents(__DIR__ . '/../../config/route.mapping.yml'));
    }
    if(array_key_exists($url, $this->routeMapping)) {
      $mapping = $this->routeMapping[$url];
      $config = $this->getRouteConfig();
      if(array_key_exists($mapping, $config)) {
        return $config[$mapping];
      } else {
        throw new \InvalidArgumentException('No route config found for route name \'' .$mapping. '\'.');
      }
    } else {
      throw new \InvalidArgumentException('No route mapping found for url \'' . $url . '\'.');
    }
  }

  /**
   * @param $controllerName
   * @param array $params
   * @return array
   */
  private function callController($controllerName, $params = [])
  {
    $controllerArr = explode('::', $controllerName);
    $class = $controllerArr[0];
    if (class_exists($class)) {
      $function = $controllerArr[1];
      $reflectionClass = new \ReflectionClass($class);
      $reflectionMethod = $reflectionClass->getMethod($function);
      $reflectionParams = $reflectionMethod->getParameters();
      $sortedParams = [];
      foreach($reflectionParams as $param) {
        $paramName = $param->getName();
        if(!array_key_exists($paramName, $params)) {
          if($param->isDefaultValueAvailable()) {
            $params[$paramName] = $param->getDefaultValue();
          } else {
            throw new \InvalidArgumentException(
              'Parameter \'' . $paramName . '\' missing for controller \'' . $controllerName .'\'.'
            );
          }
        }
        $sortedParams[$paramName] = $params[$paramName];
      }
      if ($reflectionMethod->isStatic()) {
        $val = call_user_func_array($class . '::' . $function, $sortedParams);
      } else {
        $val = call_user_func_array([new $class(), $function], $sortedParams);
      }
      if(!is_array($val)) {
        throw new \UnexpectedValueException('Controller \'' . $controllerName . '\' does not return an array.');
      } else {
        return $val;
      }
    } else {
      throw new \InvalidArgumentException('Invalid controller \'' . $controllerName . '\' called.');
    }
  }

  public function throwHTTP404() {
    http_response_code(404);
    exit;
  }

  public function route() {

    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $url = $uri_parts[0];

    try {
      $routeConfig = $this->getRouteMap($url);
    } catch (\InvalidArgumentException $e) {
      $this->throwHTTP404();
    }

    $templateName = $routeConfig['template'];
    $controller = $routeConfig['controller'];

    $variables = $this->callController($controller, $_GET);

    $templateController = new TemplateController();

    return $templateController->getTemplate($templateName, $variables);

  }

}