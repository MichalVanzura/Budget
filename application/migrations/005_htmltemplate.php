<?php

class Migration_HtmlTemplate extends CI_Migration {

    public function up() {
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('html_template', TRUE);
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("html_template_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('html_template_field', TRUE);
        
        $this->db->query('ALTER TABLE `html_template_field` ADD FOREIGN KEY (`html_template_id`) REFERENCES html_template(`id`)');
        
        $this->db->insert('html_template', array('name' => 'přehled'));
        $id = $this->db->insert_id();
        $this->db->insert('html_template_field', array('name' => 'Příjmy', 'html_template_id' => $id));
        $this->db->insert('html_template_field', array('name' => 'Výdaje', 'html_template_id' => $id));
        $this->db->insert('html_template_field', array('name' => 'Přehled', 'html_template_id' => $id));
        $this->db->insert('html_template_field', array('name' => 'Saldo hospodaření', 'html_template_id' => $id));
        
        $this->db->insert('html_template', array('name' => 'hospodaření'));
        $id = $this->db->insert_id();
        $this->db->insert('html_template_field', array('name' => 'Graf', 'html_template_id' => $id));
        $this->db->insert('html_template_field', array('name' => 'Tabulka', 'html_template_id' => $id));
        
        $this->db->insert('html_template', array('name' => 'kapitola'));
        $id = $this->db->insert_id();
        $this->db->insert('html_template_field', array('name' => 'Tabulka', 'html_template_id' => $id));
        
    }

    public function down() {
        $this->dbforge->drop_table('html_template');
        $this->dbforge->drop_table('html_template_field');
    }

}
