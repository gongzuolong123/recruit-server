<?php


/**
 * @property int $id
 * @property int $phone
 * @property int $industry_id
 * @property int $area_id
 * @property string $address
 * @property string $license_path
 * @property string $id_number
 */
class UserProfileModel extends TCModelBase {

  public function __construct() {
    $this->phone = 0;
    $this->industry_id = 0;
    $this->area_id = 0;
    $this->license_path = '';
    $this->id_number = '';
  }

  public static function tableName() {
    return 'user_profile';
  }

  protected function attributesForInsert() {
    return array('id', 'phone', 'industry_id', 'area_id', 'address', 'license_path',
      'id_number');
  }
}