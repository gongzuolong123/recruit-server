<?php
/**
 * Created by PhpStorm.
 * User: gzl
 * Date: 2018/7/24
 * Time: 下午11:50
 * @property int $id
 * @property string $name
 * @property int $area_id
 * @property string $address
 * @property int $industry_id
 * @property string $license
 * @property string $shop_name
 */
class EnterpriseModel extends TCModelBase {

  public function __construct() {
    $this->name = '';
    $this->area_id = 0;
    $this->industry_id = 0;
    $this->license = '';
    $this->shop_name = '';
  }

  public static function tableName() {
    return 'enterprises';
  }

  protected function attributesForInsert() {
    return array('name', 'area_id', 'address', 'industry_id', 'license', 'shop_name');
  }
}