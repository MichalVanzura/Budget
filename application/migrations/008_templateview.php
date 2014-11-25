<?php

class Migration_TemplateView extends CI_Migration {

    public function up() {
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("view_id int(11) unsigned");
        $this->dbforge->add_field("join_view_id int(11) unsigned");
        $this->dbforge->add_field("view_field_id int(11) unsigned");
        $this->dbforge->add_field("template_id int(11) unsigned");
        $this->dbforge->add_field("view_field_value varchar(255)");
        $this->dbforge->add_field("type varchar(20) NOT NULL DEFAULT 'col'");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('template_view', TRUE);
        
        $this->db->query('ALTER TABLE `template_view` ADD FOREIGN KEY (`view_id`) REFERENCES budget_view_field(`id`)');
        $this->db->query('ALTER TABLE `template_view` ADD FOREIGN KEY (`join_view_id`) REFERENCES budget_join_view_field(`id`)');
        $this->db->query('ALTER TABLE `template_view` ADD FOREIGN KEY (`view_field_id`) REFERENCES budget_view_field(`id`)');
        $this->db->query('ALTER TABLE `template_view` ADD FOREIGN KEY (`template_id`) REFERENCES template(`id`)');
    }

    public function down() {
        $this->dbforge->drop_table('template_view');
    }

}

