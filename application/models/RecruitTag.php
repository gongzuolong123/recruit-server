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
  

  protected function attributesForInsert() {
    return array('recruit_id', 'tag_id');
  }
}