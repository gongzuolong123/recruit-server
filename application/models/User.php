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

  public static function current() {
    $token = $_GET['token'];
    if(empty($token)) $token = $_POST['token'];
    if(empty($token)) return null;
    UserTokenModel::withCache(3600);
    $model = UserTokenModel::findByAttributes(['token' => $token]);
    if(!$model) return null;
    UserModel::withCache(3600);

    return UserModel::findById($model->id);
  }

  protected function attributesForInsert() {
    return array('created_at', 'phone_number');
  }
}