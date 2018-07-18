<?php


/**
 * @property int $id
 * @property string $weixin_id
 * @property string $created_at
 */
class UserModel extends TCModelBase {

  public function __construct() {
    $this->weixin_id = '';
  }

  public static function tableName() {
    return 'users';
  }

  protected function attributesForInsert() {
    return array('weixin_id', 'created_at');
  }
}