<?php

class M20180822_160836_add_column extends TCMigrationBase {
  public function up() {
    $this->addColumn('areas', 'has_sub_area', 'tinyint not null default 0');
  }

  public function down() {
    $this->dropColumn('areas', 'has_sub_area');
  }
}