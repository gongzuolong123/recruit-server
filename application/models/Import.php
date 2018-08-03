<?php


/**
 * @property int $id
 * @property string $file_name
 * @property string $file_path
 * @property int $status
 * @property string $created_at
 */
class ImportModel extends TCModelBase {

  public function __construct() {
    $this->file_name = '';
    $this->file_path = '';
    $this->status = -1;
  }

  public static function tableName() {
    return 'imports';
  }


  protected function attributesForInsert() {
    return array('file_name', 'file_path', 'status', 'created_at');
  }
}