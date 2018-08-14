<?php
/**
 * Created by PhpStorm.
 * User: gzl
 * Date: 2018/8/13
 * Time: 下午10:54
 * @property int $recruit_id
 * @property int $tag_id
 */

class RecruitTagModel extends TCModelBase {

  public function __construct() {
    $this->recruit_id = 0;
    $this->tag_id = 0;
  }

  public static function tableName() {
    return 'recruit_tags';
  }

  public static function getTagNamesByRecruitId($recruit_id) {
    $names = [];
    $sql = "select t.* from tags as t join recruit_tags as r on t.id = r.tag_id where r.recruit_id=" . $recruit_id;
    $models = TagModel::findAllBySql($sql);
    foreach($models as $model) {
      $names[] = $model->name;
    }

    return $names;
  }

  public static function setTagNamesByRecruitId($recruit_id, $names) {
    $sql = "delete from recruit_tags where recruit_id=" . $recruit_id;
    TCDbManager::getInstance()->db->exec($sql);
    if(is_array($names)) {
      $names = array_unique($names);
      foreach($names as $name) {
        $tag_id = TagModel::findOrCreateByName($name);
        $sql = "insert into recruit_tags (recruit_id,tag_id) values ({$recruit_id},{$tag_id})";
        TCDbManager::getInstance()->db->exec($sql);
      }
    }
  }


  protected function attributesForInsert() {
    return array('recruit_id', 'tag_id');
  }
}