<?php

class M20180815_155411_add_column_fortable extends TCMigrationBase {
  public function up() {
    $this->dropColumn('users', 'weixin_id');
    $this->addColumn('users','phone_number','bigint not null default 0');
    $this->createIndex('phone_number','users','phone_number');
    $this->dropTable('user_profile');
  }

  public function down() {
  }
}