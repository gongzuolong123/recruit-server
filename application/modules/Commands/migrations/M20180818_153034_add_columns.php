<?php

class M20180818_153034_add_columns extends TCMigrationBase {
  public function up() {
    $this->addColumn('enterprises', 'contacts_name', 'varchar(20) not null default ""');
    $this->addColumn('enterprises', 'contacts_phone', 'varchar(20) not null default ""');
    $this->addColumn('enterprises', 'status', 'tinyint not null default 0');
  }

  public function down() {
    $this->dropColumn('enterprises', 'contacts_name');
    $this->dropColumn('enterprises', 'contacts_phone');
    $this->dropColumn('enterprises', 'status');
  }
}