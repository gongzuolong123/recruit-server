
<?php

/**
 * @property int $id
 * @property string $name
 * @property string $image_path
 * @property int $weight
 */
class BannerModel extends TCModelBase {

  public function __construct() {
    $this->name = '';
    $this->image_path = '';
    $this->weight = 0;
  }

  public static function tableName() {
    return 'banners';
  }


  protected function attributesForInsert() {
    return array('name', 'image_path', 'weight');
  }
}