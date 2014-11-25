<?php

class Migration_HtmlTemplate extends CI_Migration {

    public function up() {
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("url varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('html_template', TRUE);
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("code_name varchar(20) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("html_template_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('html_template_field', TRUE);
        
        $this->db->query('ALTER TABLE `html_template_field` ADD FOREIGN KEY (`html_template_id`) REFERENCES html_template(`id`)');
        
        $this->db->insert('html_template', array('name' => 'přehled', 'url' => 'global/template/overview.html'));
        $id = $this->db->insert_id();
        $this->db->insert('html_template_field', array('name' => 'Příjmy', 'code_name' => 'income', 'html_template_id' => $id));
        $this->db->insert('html_template_field', array('name' => 'Výdaje', 'code_name' => 'outcome', 'html_template_id' => $id));
        $this->db->insert('html_template_field', array('name' => 'Přehled', 'code_name' => 'overview', 'html_template_id' => $id));
        $this->db->insert('html_template_field', array('name' => 'Saldo hospodaření', 'code_name' => 'saldo', 'html_template_id' => $id));
        
        $this->db->insert('html_template', array('name' => 'hospodaření', 'url' => 'global/template/management.html'));
        $id = $this->db->insert_id();
        $this->db->insert('html_template_field', array('name' => 'Graf', 'code_name' => 'chart', 'html_template_id' => $id));
        $this->db->insert('html_template_field', array('name' => 'Tabulka', 'code_name' => 'table', 'html_template_id' => $id));
        
        $this->db->insert('html_template', array('name' => 'kapitola', 'url' => 'global/template/chapter.html'));
        $id = $this->db->insert_id();
        $this->db->insert('html_template_field', array('name' => 'Tabulka', 'code_name' => 'chart', 'html_template_id' => $id));
        
    }

    public function down() {
        $this->dbforge->drop_table('html_template');
        $this->dbforge->drop_table('html_template_field');
    }

}
