<?php


/**
 * @property int $id
 * @property string $weixin_id
 * @property string $created_at
 * @property int $phone_number
 * @property int $enterprise_id
 * @property int $type
 */
class UserModel extends TCModelBase {

  const TYPE_B = 1;  // b端用户
  const TYPE_C = 2;  // c端用户

  public function __construct() {
    $this->phone_number = 0;
    $this->enterprise_id = 0;
    $this->type = 0;
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

  public function getProfile() {
    return UserPorfileModel::findById($this->id);
  }

  protected function attributesForInsert() {
    return array('created_at', 'phone_number', 'enterprise_id', 'type');
  }
}