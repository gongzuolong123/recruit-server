<?php
/**
 * Created by PhpStorm.
 * User: gzl
 * Date: 2018/7/24
 * Time: 下午11:50
 * @property int $id
 * @property int $enterprise_id
 * @property string $work_address
 * @property string $work_post
 * @property string $work_require
 * @property string $wages
 * @property string $contacts_name
 * @property string $contacts_phone
 * @property int $weight
 * @property int $status
 * @property int $wages_1
 * @property int $wages_2
 * @property int $wages_type
 * @property int $education
 * @property string $updated_at
 */

class RecruitModel extends TCModelBase {

  const WAGES_TYPE_MONTH = 1;  //月薪
  const WAGES_TYPE_YEAR = 2;   //年薪

  public function __construct() {
    $this->enterprise_id = 0;
    $this->weight = 0;
    $this->status = 0;
    $this->contacts_name = '';
    $this->contacts_phone = '';
    $this->wages_1 = 0;
    $this->wages_2 = 0;
    $this->wages_type = 0;
    $this->education = 0;
  }

  public static function tableName() {
    return 'recruits';
  }

  public function getEnterpriseModel() {
    if(!$this->_vars['enterpriseModel']) $this->_vars['enterpriseModel'] = EnterpriseModel::findById($this->enterprise_id);
    return $this->_vars['enterpriseModel'];
  }

  protected function attributesForInsert() {
    return array('enterprise_id', 'work_address', 'work_post', 'work_require',
      'wages', 'weight', 'status', 'contacts_name', 'contacts_phone',
      'wages_1', 'wages_2', 'wages_type', 'education', 'updated_at');
  }
}