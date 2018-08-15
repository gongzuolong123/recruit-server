<?php


/**
 * @property int $id
 * @property string $weixin_id
 * @property string $created_at
 * @property int $phone_number
 */
class UserModel extends TCModelBase {

  public function __construct() {
    $this->phone_number = 0;
  }

  public static function tableName() {
    return 'users';
  }

  protected function attributesForInsert() {
    return array('created_at', 'phone_number');
  }
}