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

  public static function findOrCreateByName($name){
    if(empty($name)) return 0;
    $model = self::findByAttributes(['name'=>$name]);
    if(!$model) {
      $model = new self();
      $model->name = $name;
      $model->status = 0;
      $model->insert();
    }
    return $model->id;
  }

  protected function attributesForInsert() {
    return array('name', 'status');
  }
}