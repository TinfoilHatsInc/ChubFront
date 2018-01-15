<?php

namespace core\playback;

use core\database\DatabaseController;
use core\routing\RoutingController;

class PlaybackController
{

  /**
   * @var DatabaseController
   */
  private $databaseController;

  public function __construct()
  {
    $this->databaseController = new DatabaseController();
  }

  public function build()
  {

    $oldEvents = [];

    $rooms = $this->databaseController->getAllRoomModules();

    foreach ($rooms as $room) {
      foreach ($room['Events'] as $event) {
        $timestamp = new \DateTime($event['Datetime']);
        $diff = (new \DateTime())->diff($timestamp);
        $formattedEvent = self::formatEvent($room['ID'], $room['Name'], $event);
        if ($diff->m > 0) {
          $oldEvents[] = $formattedEvent;
        }
      }
    }

    return [
      'oldEvents' => $oldEvents,
    ];
  }

  public static function formatEvent($roomId, $roomName, array $event)
  {
    $formattedVideos = [];
    $fileLocations = array_column($event['Recordings'], 'File_Location');
    foreach($fileLocations as $fileLocation) {
      $videos = glob(__DIR__ . '/../..' . $fileLocation . '/*.mp4');
      $videos = array_map(function ($name) use ($fileLocation) {
        return $fileLocation . '/' . basename($name);
      }, $videos);
      $formattedVideos = array_merge($formattedVideos, $videos);
    }
    $timestamp = new \DateTime($event['Datetime']);
    return [
      'eventId' => $event['ID'],
      'date' => $timestamp,
      'room' => $roomName,
      'roomId' => $roomId,
      'isCritical' => $event['Important'],
      'videos' => $formattedVideos,
    ];
  }

  public function getNewEvents()
  {
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
      $events = [];

      $rooms = $this->databaseController->getAllRoomModules();

      foreach ($rooms as $room) {
        foreach ($room['Events'] as $event) {
          $timestamp = new \DateTime($event['Datetime']);
          $diff = (new \DateTime())->diff($timestamp);
          $event = self::formatEvent($room['ID'], $room['Name'], $event);
          if ($diff->m == 0) {
            $events[] = [
              'id' => $event['eventId'],
              'title' => '',
              'start' => $timestamp->format(DATE_ATOM),
              'isCritical' => $event['isCritical'],
              'allDay' => FALSE,
              'videos' => $event['videos'],
              'eventId' => $event['eventId'],
              'room' => $event['room'],
              'roomId' => $event['roomId'],
          ];
          }
        }
      }

      echo json_encode($events);
      exit;
    } else {
      RoutingController::getInstance()->throwHTTP404();
    }
  }

}