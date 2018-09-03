<?php

class M20180903_153031_add_column extends TCMigrationBase {
  public function up() {
    //$this->addColumn('recruits', 'refresh_time', 'datetime');
    $this->createIndex('refresh_time','recruits','refresh_time');
  }

  public function down() {
    $this->dropColumn('recruits', 'refresh_time');
  }
}