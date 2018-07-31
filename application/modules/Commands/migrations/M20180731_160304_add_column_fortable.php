<?php

class M20180731_160304_add_column_fortable extends TCMigrationBase {
  public function up() {
    $this->addColumn('recruits','wages_1','smallint not null default 0');
    $this->addColumn('recruits','wages_2','smallint not null default 0');
    $this->addColumn('recruits','wages_type','tinyint not null default 1');
    $this->addColumn('recruits','education','tinyint not null default 0');
    $this->addColumn('recruits','updated_at','datetime');
  }
  
  public function down() {
    $this->dropColumn('recruits','wages_1');
    $this->dropColumn('recruits','wages_2');
    $this->dropColumn('recruits','wages_type');
    $this->dropColumn('recruits','education');
    $this->dropColumn('recruits','updated_at');
  }
}