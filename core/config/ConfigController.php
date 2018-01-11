<?php

namespace core\config;

use core\database\DatabaseController;

class ConfigController
{

  private $databaseController;

  public function __construct()
  {
    $this->databaseController = new DatabaseController();
  }

  public function build() {

    $modules = $this->databaseController->getAllModules();
    $rooms = $this->databaseController->getAllRooms();

    return [
      'unconfiguredModules' => $modules,
      'rooms' => $rooms,
    ];
  }

}