
<?php
/**
 * Created by PhpStorm.
 * User: gzl
 * Date: 2018/10/9
 * Time: 下午10:02
 */

class UserPorfileModel extends TCModelBase {

  public function __construct() {
  }

  public static function tableName() {
    return 'user_profiles';
  }


  protected function attributesForInsert() {
    return array();
  }
}