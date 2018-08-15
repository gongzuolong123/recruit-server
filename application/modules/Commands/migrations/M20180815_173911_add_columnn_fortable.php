<?php

class M20180815_173911_add_columnn_fortable extends TCMigrationBase {
  public function up() {
    $this->addColumn('users','enterprise_id','int not null default 0');
  }
  
  public function down() {
    $this->dropColumn('users','enterprise_id');
  }
}