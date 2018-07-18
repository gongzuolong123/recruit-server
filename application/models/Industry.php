<?php


/**
 * @property int $id
 * @property string $name
 * @property int $status
 */
class IndustryModel extends TCModelBase {

  public function __construct() {
    $this->status = 0;
  }

  public static function tableName() {
    return 'industry';
  }

  protected function attributesForInsert() {
    return array('name', 'status');
  }
}