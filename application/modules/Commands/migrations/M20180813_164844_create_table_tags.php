<?php

class M20180813_164844_create_table_tags extends TCMigrationBase {
  public function up() {
    $this->createTable('tags', [
      'id' => 'integer not null primary key auto_increment',
      'name' => 'varchar(20)',
    ]);

    $this->createTable('recruit_tags', [
      'recruit_id' => 'int not null default 0',
      'tag_id' => 'int not null default 0',
      'key recruit_id (recruit_id)',
      'key tag_id (tag_id)',
    ]);
  }

  public function down() {
    $this->dropTable('tags');
    $this->dropTable('recruit_tags');
  }
}