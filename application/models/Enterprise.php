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
 * @property string $contacts_name
 * @property string $contacts_phone
 * @property int $status
 */
class EnterpriseModel extends TCModelBase {

  const STATUS_NOMAL = 0;   // 正常
  const STATUS_DELETE = -1; // 删除
  const STATUS_REVIEW = 1;  // 待审核

  public function __construct() {
    $this->name = '';
    $this->area_id = 0;
    $this->industry_id = 0;
    $this->license = '';
    $this->shop_name = '';
    $this->contacts_name = '';
    $this->contacts_phone = '';
    $this->status = 0;
  }

  public static function tableName() {
    return 'enterprises';
  }

  protected function attributesForInsert() {
    return array('name', 'area_id', 'address', 'industry_id', 'license', 'shop_name',
      'contacts_name', 'contacts_phone', 'status');
  }
}