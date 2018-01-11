<?php

namespace core\database;

class QueryParam
{

  const TYPE_STRING = 's';
  const TYPE_INTEGER = 'i';
  const TYPE_DOUBLE = 'd';
  const TYPE_BLOB = 'b';

  /**
   * @var string
   */
  private $type;
  /**
   * @var string
   */
  private $value;

  /**
   * QueryParam constructor.
   * @param string $type
   * @param string $value
   */
  public function __construct($type, $value)
  {
    $this->type = $type;
    $this->value = $value;
  }

  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }

}