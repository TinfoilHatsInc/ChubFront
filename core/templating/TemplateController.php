<?php

namespace core\templating;

use Twig_Environment;
use Twig_Loader_Filesystem;

class TemplateController
{

  public function getTemplate($templateName, $variables = []) {

    $templateRoot = __DIR__ . '/../../templates';
    $dirs = array_merge(array($templateRoot), array_filter(glob($templateRoot . '/*'), 'is_dir'));

    $loader = new Twig_Loader_Filesystem($dirs);

    $twig = new Twig_Environment($loader, [
      'cache' => 'cache',
    ]);

    $twig->addExtension(new \Twig_Extension_StringLoader());

    return $twig->render('template.twig', [
      'content' => $twig->render($templateName . '.twig', $variables),
    ]);
  }

}