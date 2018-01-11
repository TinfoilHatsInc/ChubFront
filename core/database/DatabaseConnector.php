<?php

namespace core\database;

use core\common\PythonExecuter;
use core\common\Singleton;
use PHPSQLParser\PHPSQLParser;

class DatabaseConnector extends Singleton
{

  /**
   * @param $query
   * @param QueryParam[] $params
   * @return mixed
   */
  private function executeStatement($query, array $params)
  {

    $parser = new PHPSQLParser();
    $parsed = $parser->parse($query);

    $from = [];

    foreach($parsed['FROM'] as $fromItem) {
      $from[] = $fromItem['table'];
    }

    $select = [];

    foreach($parsed['SELECT'] as $selectItem) {
      if($selectItem['expr_type'] == 'colref') {
        $arr = explode('.', $selectItem['base_expr']);
        if (count($arr) > 1) {
          $table = $arr[0];
          $select[$table][] = $arr[1];
        } else {
          $select[$from[0]][] = $arr[0];
        }
      }
    }
    if(array_key_exists('WHERE', $parsed)) {
      $whereContents = $parsed['WHERE'];
    } else {
      $whereContents = [];
    }

    $where = [];

    $paramIndex = 0;

    for($i = 0; $i < count($whereContents); $i++) {
      $whereItem = $whereContents[$i];
      if($whereItem['expr_type'] == 'colref') {
        $arr = explode('.', $whereItem['base_expr']);
        if (count($arr) > 1) {
          $table = $arr[0];
          $column = $arr[1];
        } else {
          $table = $from[0];
          $column = $arr[0];
        }
        if(array_key_exists($i+1, $whereContents) && $whereContents[$i+1]['expr_type'] == 'operator') {
          $value = $whereContents[$i+2]['base_expr'];
          if($value == '?') {
            $value = $params[$paramIndex]->getValue();
          }
          $where[$table][$column]['where'][] = [
            $whereContents[$i+1]['base_expr'],
            $value,
          ];
          $i += 2;
        }
      }
    }

    $database = PythonExecuter::callJSONSerializer('-c');

    $result = [];

    foreach($database as $room) {
      $shouldContinue = TRUE;
      foreach($select as $name => $items) {
        switch ($name) {
          case 'module':
            $table = 'Modules';
            break;
          case 'event':
            $table = 'Events';
            break;
          default:
            $table = NULL;
            break;
        }
        if(array_key_exists($name, $where)) {
          $currentWhere = $where[$name];
          if(array_key_exists('room', $currentWhere)) {
            foreach($currentWhere['room']['where'] as $item) {
              if(!$this->checkOperatorValue($item[0], $item[1], $room['ID'])) {
                $shouldContinue = FALSE;
                break;
              }
            }
          } else {
            $properties = array_keys($currentWhere);
            foreach($properties as $property) {
              foreach($currentWhere[$property]['where'] as $item) {
                if(empty($table)) {
                  $selecting = $room;
                } else {
                  $selecting = $room[$table];
                }
                if(!$this->checkOperatorValue($item[0], $item[1], $selecting[ucfirst($property)])) {
                  $shouldContinue = FALSE;
                  break;
                }
              }
            }
          }
          if(!$shouldContinue) {
            break;
          }
        }
        if(count($items) == 1 && $items[0] == '*') {
          if(!empty($table)) {
            $result = $room[$table];
          } else {
            $result[] = $room;
          }
        } else {
          $res = [];
          foreach($items as $item) {
            $item = ucfirst($item);
            if(!empty($table)) {
              $res[$item] = $room[$table][$item];
            } else {
              $res[$item] = $room[$item];
            }
          }
          if(!empty($res)) {
            $result[] = $res;
          }
        }
      }
    }

    return $result;

  }

  /**
   * @param $query
   * @param QueryParam[] ...$params
   * @return bool|array
   */
  public function executeSelectStatement($query, ...$params)
  {
    return $this->executeStatement($query, $params);
  }

  /**
   * @param $query
   * @param QueryParam[] ...$params
   * @return bool|array
   */
  public function executeInsertStatement($query, ...$params)
  {

    $result = $this->executeStatement($query, $params);

    return $result;

  }

  /**
   * @param $query
   * @param QueryParam[] ...$params
   * @return bool|array
   */
  public function executeUpdateStatement($query, ...$params)
  {

    $result = $this->executeStatement($query, $params);

    return $result;

  }

  /**
   * @param $query
   * @param QueryParam[] ...$params
   * @return bool|int|array
   */
  public function executeDeleteStatement($query, ...$params)
  {

    $result = $this->executeStatement($query, $params);

    return $result;

  }

  private function checkOperatorValue($operator, $expected, $trueValue) {
    switch ($operator) {
      case '=':
        return $expected == $trueValue;
      case '!=':
        return $expected != $trueValue;
    }
  }

}