<?php

class Migration_Menu extends CI_Migration {
    public function up(){
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("title varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("url varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("subject varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("parent_subject varchar(255)");
 
        $this->dbforge->add_key('id', TRUE);
       
        $this->dbforge->create_table('menu', TRUE);
    }
 
    public function down(){
        $this->dbforge->drop_table('menu');
    }
}