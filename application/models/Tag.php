
<?php
/**
 * Created by PhpStorm.
 * User: gzl
 * Date: 2018/8/13
 * Time: 下午10:52
 */
/**
 * @property int $id
 * @property string $name
 * @property int $status
 */
class TagModel extends TCModelBase {

  public function __construct() {
  }

  public static function tableName() {
    return 'tags';
  }

  public static function findOrCreateByName($name){
    if(empty($name)) return 0;
    $model = self::findByAttributes(['name'=>$name]);
    if(!$model) {
      $model = new self();
      $model->name = $name;
      $model->insert();
    }
    return $model->id;
  }

  protected function attributesForInsert() {
    return array('name');
  }
}