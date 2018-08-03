<?php

class M20180803_154312_create_table extends TCMigrationBase {
  public function up() {
    $this->createTable('imports', [
      'id' => 'integer not null primary key auto_increment',
      'file_name' => 'varchar(100) not null default ""',
      'file_path' => 'varchar(255) not null default ""',
      'status' => 'tinyint not null default -1',
      'created_at' => 'timestamp not null default current_timestamp'
    ]);
  }

  public function down() {
    $this->dropTable('imports');
  }
}