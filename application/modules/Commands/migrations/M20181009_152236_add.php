<?php

class M20181009_152236_add extends TCMigrationBase {
  public function up() {
    $this->createTable('history_call_phone', [
      'id' => 'integer not null primary key auto_increment',
      'user_id' => 'int not null default 0',
      'enterprise_id' => 'int not null default 0',
      'updated_at' => 'datetime',
      'key user_id (user_id)',
      'key updated_at (updated_at)',
      'unique key record (user_id,enterprise_id)',
    ]);
    $this->addColumn('users', 'type', 'tinyint not null default 0');
    $this->createTable('user_profiles', [
      'id' => 'integer not null primary key',
      'name' => 'varchar(255) not null default ""',
      'gender' => 'tinyint not null default 0',
      'birth_date' => 'date',
      'city' => 'varchar(255) not null default ""',
    ]);
  }

  public function down() {
    $this->dropTable('history_call_phone');
    $this->dropColumn('users', 'type');
    $this->dropTable('user_profiles');
  }
}