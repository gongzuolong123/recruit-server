<?php

class M20180808_153948_create_tables extends TCMigrationBase {
  public function up() {
    $this->createTable('advertisements', [
      'id' => 'integer not null primary key auto_increment',
      'enterprise_id' => 'int not null default 0',
      'image_path' => 'varchar(255) not null default ""',
      'title' => 'varchar(255) not null default ""',
      'describe' => 'varchar(255) not null default ""',
      'weight' => 'int not null default 0',
      'status' => 'tinyint not null default 0',
      'created_at' => 'timestamp not null default current_timestamp'
    ]);
  }
  
  public function down() {
    $this->dropTable('advertisements');
  }
}