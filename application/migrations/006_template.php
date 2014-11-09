<?php

class Migration_Template extends CI_Migration {

    public function up() {
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("slug varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("html_template_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('template', TRUE);
        
        $this->db->query('ALTER TABLE `template` ADD FOREIGN KEY (`html_template_id`) REFERENCES html_template(`id`)');
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("template_id int(11) unsigned");
        $this->dbforge->add_field("budget_view_id int(11) unsigned");
        $this->dbforge->add_field("budget_join_view_id int(11) unsigned");
        $this->dbforge->add_field("display varchar(20) NOT NULL DEFAULT 'table'");
        $this->dbforge->add_field("html_template_field_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('template_field', TRUE);
        
        $this->db->query('ALTER TABLE `template_field` ADD FOREIGN KEY (`template_id`) REFERENCES template(`id`)');
        $this->db->query('ALTER TABLE `template_field` ADD FOREIGN KEY (`budget_view_id`) REFERENCES budget_view(`id`)');
        $this->db->query('ALTER TABLE `template_field` ADD FOREIGN KEY (`budget_join_view_id`) REFERENCES budget_join_view(`id`)');
        $this->db->query('ALTER TABLE `template_field` ADD FOREIGN KEY (`html_template_field_id`) REFERENCES html_template_field(`id`)');
    }

    public function down() {
        $this->dbforge->drop_table('template');
        $this->dbforge->drop_table('template_field');
    }

}
