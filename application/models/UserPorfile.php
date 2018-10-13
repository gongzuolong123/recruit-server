
<?php
/**
 * Created by PhpStorm.
 * User: gzl
 * Date: 2018/10/9
 * Time: 下午10:02
 * @property int $id
 * @property string $name
 * @property int $gender
 * @property string $birth_date
 * @property string $city
 */

class UserPorfileModel extends TCModelBase {

  const GENDER_MAN = 1;  // 男
  const GENDER_WOMEN = 2; // 女

  public function __construct() {
    $this->name = '';
    $this->gender = 0;
    $this->city = '';
  }

  public static function tableName() {
    return 'user_profiles';
  }


  protected function attributesForInsert() {
    return array('id', 'name', 'gender', 'birth_date', 'city');
  }
}