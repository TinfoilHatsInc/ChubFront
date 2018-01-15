<?php

namespace core\database;

class DatabaseController
{

  /**
   * @var DatabaseConnector
   */
  private $databaseConnector;

  public function __construct()
  {
    $this->databaseConnector = DatabaseConnector::getInstance();
  }

  public function getAllModules() {
    return $this->databaseConnector->executeSelectStatement(
      'SELECT * FROM module WHERE module.room = ?',
      new QueryParam(QueryParam::TYPE_INTEGER, '0')
    );
  }

  public function getAllRoomNames() {
    return $this->databaseConnector->executeSelectStatement(
      'SELECT name FROM room WHERE ID != ?',
      new QueryParam(QueryParam::TYPE_INTEGER, '0')
    );
  }

  public function getAllRooms() {
    return $this->databaseConnector->executeSelectStatement(
      'SELECT * FROM room WHERE ID != ?',
      new QueryParam(QueryParam::TYPE_INTEGER, '0')
    );
  }

  public function getAllRoomModules() {
    $result = [];
    $rooms = $this->databaseConnector->executeSelectStatement(
      'SELECT * FROM room WHERE ID != 0'
    );
    foreach($rooms as $room) {
      unset($room['Modules']);
      $result[] = $room;
    }
    return $result;
  }

  public function getAllEvents() {
    $room = $this->databaseConnector->executeSelectStatement(
      'SELECT * FROM room WHERE ID = 0'
    );
    return $room[0]['Events'];
  }



}