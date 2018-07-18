<?php


/**
 * @property int $id
 * @property string $token
 * @property string $expire_time
 */
class UserTokenModel extends TCModelBase {

  public function __construct() {
    $this->token = '';
  }

  public static function tableName() {
    return 'user_tokens';
  }

  protected function attributesForInsert() {
    return array('id', 'token', 'expire_time');
  }
}