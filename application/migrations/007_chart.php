<?php

class Migration_Chart extends CI_Migration {

    public function up() {
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("view_id int(11) unsigned");
        $this->dbforge->add_field("join_view_id int(11) unsigned");
        $this->dbforge->add_field("stacking varchar(10) NOT NULL DEFAULT ''");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('chart', TRUE);
        
        $this->db->query('ALTER TABLE `chart` ADD FOREIGN KEY (`view_id`) REFERENCES budget_view(`id`)');
        $this->db->query('ALTER TABLE `chart` ADD FOREIGN KEY (`join_view_id`) REFERENCES budget_join_view(`id`)');
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("field_id int(11) unsigned");
        $this->dbforge->add_field("chart_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('chart_category', TRUE);
        
        $this->db->query('ALTER TABLE `chart_category` ADD FOREIGN KEY (`field_id`) REFERENCES budget_view_field(`id`)');
        $this->db->query('ALTER TABLE `chart_category` ADD FOREIGN KEY (`chart_id`) REFERENCES chart(`id`)');
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("field_id int(11) unsigned");
        $this->dbforge->add_field("chart_id int(11) unsigned");
        $this->dbforge->add_field("type varchar(20)");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('chart_serie', TRUE);
        
        $this->db->query('ALTER TABLE `chart_category` ADD FOREIGN KEY (`field_id`) REFERENCES budget_view_field(`id`)');
        $this->db->query('ALTER TABLE `chart_category` ADD FOREIGN KEY (`chart_id`) REFERENCES chart(`id`)');
    }

    public function down() {
        $this->dbforge->drop_table('chart');
        $this->dbforge->drop_table('chart_category');
        $this->dbforge->drop_table('chart_serie');
    }

}
