<?php

class M20180827_152550_create_table extends TCMigrationBase {
  public function up() {
    $this->createTable('banners', [
      'id' => 'integer not null primary key auto_increment',
      'name' => 'varchar(100) not null default ""',
      'image_path' => 'varchar(255) not null default ""',
      'weight' => 'int not null default 0',
    ]);
  }

  public function down() {
    $this->dropTable('banners');
  }
}