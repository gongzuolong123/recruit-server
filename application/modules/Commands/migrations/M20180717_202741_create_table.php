<?php

class M20180717_202741_create_table extends TCMigrationBase {
  public function up() {
    $this->createTable('industry', [
      'id' => 'integer not null primary key auto_increment',
      'name' => 'varchar(20)',
      'status' => 'tinyint not null default 0',
    ]);

    $this->createTable('users', [
      'id' => 'integer not null primary key auto_increment',
      'weixin_id' => 'varchar(50) not null default ""',
      'created_at' => 'timestamp not null default current_timestamp',
      'unique key weixin_id (weixin_id)',
    ]);

    $this->createTable('user_profile', [
      'id' => 'integer not null primary key',
      'phone' => 'int not null default 0',
      'industry_id' => 'int not null default 0',
      'area_id' => 'int not null default 0',
      'address' => 'text',
      'license_path' => 'varchar(255) not null default ""',
      'id_number' => 'varchar(50) not null default ""',
      'key phone (phone)',
      'key area_id (area_id)',
    ]);

    $this->createTable('user_tokens',[
      'id' => 'integer not null primary key',
      'token' => 'char(32) not null default ""',
      'expire_time' => 'datetime',
      'key token (token)'
    ]);
  }

  public function down() {
    $this->dropTable('industry');
    $this->dropTable('users');
    $this->dropTable('user_profile');
    $this->dropTable('user_tokens');
  }
}