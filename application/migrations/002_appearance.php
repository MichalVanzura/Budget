<?php

class Migration_Appearance extends CI_Migration {
    public function up(){
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("logo_path varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("header_color varchar(10) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("subject int(11) unsigned NOT NULL");
 
        $this->dbforge->add_key('id', TRUE);
       
        $this->dbforge->create_table('appearance', TRUE);
    }
 
    public function down(){
        $this->dbforge->drop_table('appearance');
    }
}