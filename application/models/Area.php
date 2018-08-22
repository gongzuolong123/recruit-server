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

  public static function getAllAreaName($id, $level = 0) {
    $names = [];
    while($model = self::findById($id)) {
      if(!$model) break;
      $names[] = $model->name;
      if($model->level == $level) break;
      $id = $model->parent_id;
    }

    return implode(',', array_reverse($names));
  }

  public static function getAllAreaId($id, $level = 0) {
    $ids = [];
    while($model = self::findById($id)) {
      if(!$model) break;
      $ids[] = $model->id;
      if($model->level == $level) break;
      $id = $model->parent_id;
    }

    return implode(',', array_reverse($ids));
  }
}