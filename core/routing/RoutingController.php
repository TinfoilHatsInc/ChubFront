<?php

namespace core\routing;

use core\common\Singleton;
use core\templating\TemplateController;
use Symfony\Component\Yaml\Yaml;
use TinfoilHMAC\Exception\MissingSharedKeyException;
use TinfoilHMAC\Util\Session;
use TinfoilHMAC\Util\UserSession;

class RoutingController extends Singleton
{

  private $routeConfig;
  private $routeMapping;
  private $activeRouteConfig;

  /**
   * @return array
   */
  public function getRouteConfig()
  {
    if (empty($this->routeConfig)) {
      $this->routeConfig = Yaml::parse(file_get_contents(__DIR__ . '/../../config/route.config.yml'));
    }
    return $this->routeConfig;
  }

  public function getRouteMappings()
  {
    if (empty($this->routeMapping)) {
      $this->routeMapping = Yaml::parse(file_get_contents(__DIR__ . '/../../config/route.mapping.yml'));
    }
    return $this->routeMapping;
  }

  /**
   * @param $url
   * @return array
   */
  private function getRouteMap($url)
  {
    $routeMapping = $this->getRouteMappings();
    if (array_key_exists($url, $routeMapping)) {
      $mapping = $routeMapping[$url];
      $config = $this->getRouteConfig();
      if (array_key_exists($mapping, $config)) {
        $routeConfig = array_merge([
          'routeName' => $mapping,
        ], $config[$mapping]);
        $this->activeRouteConfig = $routeConfig;
        return $routeConfig;
      } else {
        throw new \InvalidArgumentException('No route config found for route name \'' . $mapping . '\'.');
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
      foreach ($reflectionParams as $param) {
        $paramName = $param->getName();
        if (!array_key_exists($paramName, $params)) {
          if ($param->isDefaultValueAvailable()) {
            $params[$paramName] = $param->getDefaultValue();
          } else {
            throw new \InvalidArgumentException(
              'Parameter \'' . $paramName . '\' missing for controller \'' . $controllerName . '\'.'
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
      if (!is_array($val)) {
        throw new \UnexpectedValueException('Controller \'' . $controllerName . '\' does not return an array.');
      } else {
        return $val;
      }
    } else {
      throw new \InvalidArgumentException('Invalid controller \'' . $controllerName . '\' called.');
    }
  }

  public function throwHTTP404()
  {
    http_response_code(404);
    exit;
  }

  public function route()
  {

    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $url = $uri_parts[0];

    if ($url == '/') {
      $this->redirectRoute('home');
      exit;
    }

    try {
      $routeConfig = $this->getRouteMap($url);
    } catch (\InvalidArgumentException $e) {
      $this->throwHTTP404();
    }

    // Check if there is a known shared key registered.
    if (!Session::getInstance()->hasKnownSharedKey() || !Session::getInstance()->sharedKeyIsValid()) {
      if ($this->getActiveRouteName() != 'login') {
        // If no key is registered force the user to login.
        $this->redirectRoute('login');
      }
    } else {
      if ($this->getActiveRouteName() == 'login') {
        $this->redirectRoute('home');
      }
    }

    if (array_key_exists('template', $routeConfig)) {
      $templateName = $routeConfig['template'];
    } else {
      $templateName = '';
    }

    $controller = $routeConfig['controller'];

    $variables = $this->callController($controller, $_GET);

    $templateController = new TemplateController();

    return $templateController->getTemplate($templateName, $variables);

  }

  public function redirectRoute($routeName, $params = [])
  {

    $mappings = array_flip($this->getRouteMappings());

    if (array_key_exists($routeName, $mappings)) {
      header('Location: ' . $mappings[$routeName]);
    } else {
      throw new \InvalidArgumentException('Route with name \'' . $routeName . '\' could not be found.');
    }

  }

  public function getActiveRouteName()
  {
    return $this->activeRouteConfig['routeName'];
  }

}