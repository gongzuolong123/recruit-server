<?php

class M20180717_150015_create_area_table extends TCMigrationBase {
  public function up() {
    $this->createTable('areas', [
      'id' => 'integer not null primary key auto_increment',
      'name' => 'varchar(20)',
      'level' => 'tinyint not null default 0',
      'parent_id' => 'int not null default 0',
      'key parent_id (parent_id)',
    ]);
  }

  public function down() {
    $this->dropTable('areas');
  }
}