<?php

require_once 'vendor/autoload.php';

spl_autoload_register(function ($class_name) {
  $class = $class_name . '.php';
  $class = str_replace('\\', '/', $class);
  require_once $class;
});