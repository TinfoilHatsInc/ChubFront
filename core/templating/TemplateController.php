<?php

namespace core\templating;

use core\routing\RouteUtils;
use Twig_Environment;
use Twig_Loader_Filesystem;

class TemplateController
{

  public function getTemplate($templateName, $variables = []) {

    $templateRoot = __DIR__ . '/../../templates';
    $dirs = array_merge(array($templateRoot), array_filter(glob($templateRoot . '/*'), 'is_dir'));

    $loader = new Twig_Loader_Filesystem($dirs);

    $twig = new Twig_Environment($loader, [
//      'cache' => 'cache',
    ]);

    $twig->addFunction(new \Twig_SimpleFunction('link', array(RouteUtils::class, 'link'), array('is_safe' => array('html'))));
    $twig->addExtension(new \Twig_Extension_StringLoader());

    return $twig->render('template.twig', [
      'content' => $twig->render($templateName . '.twig', $variables),
    ]);
  }

}