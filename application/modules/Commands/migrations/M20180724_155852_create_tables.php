<?php

class M20180724_155852_create_tables extends TCMigrationBase {
  public function up() {
    $this->createTable('enterprises', [
      'id' => 'integer not null primary key auto_increment',
      'name' => 'varchar(100) not null default ""',
      'area_id' => 'int not null default 0',
      'address' => 'text',
      'industry_id' => 'int not null default 0',
      'license' => 'varchar(255) not null default ""',
      'shop_name' => 'varchar(100) not null default ""',
      'key area_id (area_id)',
      'key industry_id (industry_id)',
    ]);

    $this->createTable('recruits', [
      'id' => 'integer not null primary key auto_increment',
      'enterprise_id' => 'int not null default 0',
      'work_address' => 'text',
      'work_post' => 'text',
      'work_require' => 'text',
      'wages' => 'text',
      'weight' => 'int not null default 0',
      'status' => 'tinyint not null default 0',
      'contacts_name' => 'varchar(20) not null default ""',
      'contacts_phone' => 'varchar(20) not null default ""',
      'key enterprise_id (enterprise_id)',
      'key weight (weight)',
    ]);
  }

  
  public function down() {
    $this->dropTable('enterprises');
    $this->dropTable('recruits');
  }
}