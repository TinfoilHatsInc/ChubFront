<?php

namespace core\routing;

class RouteUtils
{

  public static function getPath($routeName) {
    $routeMappings = array_flip(RoutingController::getInstance()->getRouteMappings());
    if(array_key_exists($routeName, $routeMappings)) {
      return $routeMappings[$routeName];
    } else {
      return NULL;
    }
  }

  public static function link($text, $classes = [], $routeName, $params = []) {
    if(is_string($classes)) {
      $classes = explode(' ', $classes);
    }

    $routePath = self::getPath($routeName);

    if(!empty($routePath)) {
      if(RoutingController::getInstance()->getActiveRouteName() == $routeName) {
        $classes[] = 'active';
      }
      $link = '<a';
      if(!empty($classes)) {
        $link .= ' class="' . implode(' ', $classes) . '"';
      }
      $link .= ' href="' . $routePath . '"';
      $link .= '>' . $text . '</a>';
      return $link;
    } else {
      return '';
    }

  }

}