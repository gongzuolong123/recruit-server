<?php

/**
 * @property int $id
 * @property string $name
 * @property int $level
 * @property int $parent_id
 */
class AreaModel extends TCModelBase {

  public function __construct() {
    $this->level = 0;
    $this->parent_id = 0;
  }

  public static function tableName() {
    return 'areas';
  }

  protected function attributesForInsert() {
    return array('name', 'level', 'parent_id');
  }
}