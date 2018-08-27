<?php

/**
 * @property int $id
 * @property string $name
 * @property int $level
 * @property int $parent_id
 * @property int $has_sub_area
 */
class AreaModel extends TCModelBase {

  public function __construct() {
    $this->level = 0;
    $this->parent_id = 0;
    $this->has_sub_area = 0;
  }

  public static function tableName() {
    return 'areas';
  }

  protected function attributesForInsert() {
    return array('name', 'level', 'parent_id', 'has_sub_area');
  }

  public static function getAllAreaName($id, $level = 0) {
    $cache_key = 'all_area_name_id_' . $id . '_level_' . $level;
    $row = TCMemcachedManager::getInstance()->cache->get($cache_key);
    if($row !== false) return $row;
    $names = [];
    while($model = self::findById($id)) {
      if(!$model) break;
      $names[] = $model->name;
      if($model->level == $level) break;
      $id = $model->parent_id;
    }
    $row = implode(',', array_reverse($names));
    TCMemcachedManager::getInstance()->cache->set($cache_key, $row, 86400);

    return $row;
  }

  public static function getAllAreaId($id, $level = 0) {
    $cache_key = 'all_area_id_id_' . $id . '_level_' . $level;
    $row = TCMemcachedManager::getInstance()->cache->get($cache_key);
    if($row !== false) return $row;
    $ids = [];
    while($model = self::findById($id)) {
      if(!$model) break;
      $ids[] = $model->id;
      if($model->level == $level) break;
      $id = $model->parent_id;
    }
    $row = implode(',', array_reverse($ids));
    TCMemcachedManager::getInstance()->cache->set($cache_key, $row, 86400);

    return $row;
  }
}