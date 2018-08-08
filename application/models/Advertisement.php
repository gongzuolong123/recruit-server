<?php


/**
 * @property int $id
 * @property string $image_path
 * @property string $title
 * @property string $describe
 * @property int $weight
 * @property int $status
 * @property string $created_at
 * @property int $enterprise_id
 */
class AdvertisementModel extends TCModelBase {

  public function __construct() {
    $this->enterprise_id = 0;
    $this->image_path = '';
    $this->title = '';
    $this->describe = '';
    $this->weight = 0;
    $this->status = 0;
  }

  public static function tableName() {
    return 'advertisements';
  }


  protected function attributesForInsert() {
    return array('enterprise_id', 'image_path', 'title', 'describe', 'weight',
      'status', 'created_at');
  }
}