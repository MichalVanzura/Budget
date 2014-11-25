<?php

class Migration_BudgetView extends CI_Migration {

    public function up() {
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("key_field varchar(255)");
        $this->dbforge->add_field("distinct_rows tinyint(1) unsigned NOT NULL");
        $this->dbforge->add_field("from_table varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_view', TRUE);

        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("table_name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("alias varchar(255)");
        $this->dbforge->add_field("function varchar(10)");
        $this->dbforge->add_field("formatter varchar(10)");
        $this->dbforge->add_field("color varchar(7)");
        $this->dbforge->add_field("budget_view_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_view_field', TRUE);

        $this->db->query('ALTER TABLE `budget_view_field` ADD FOREIGN KEY (`budget_view_id`) REFERENCES budget_view(`id`)');
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("color varchar(7) NOT NULL");
        $this->dbforge->add_field("budget_view_field_id int(11) unsigned");
        $this->dbforge->add_field("budget_view_field_value varchar(255) NOT NULL");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_view_field_color', TRUE);
        
        $this->db->query('ALTER TABLE `budget_view_field_color` ADD FOREIGN KEY (`budget_view_field_id`) REFERENCES budget_view_field(`id`)');

        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("first_table varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("second_table varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("first_field varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("second_field varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("budget_view_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_view_join', TRUE);
        
        $this->db->query('ALTER TABLE `budget_view_join` ADD FOREIGN KEY (`budget_view_id`) REFERENCES budget_view(`id`)');

        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("table_name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("field varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("operator varchar(10) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("value varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("rel varchar(10) NOT NULL DEFAULT 'and'");
        $this->dbforge->add_field("budget_view_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_view_where', TRUE);
        
        $this->db->query('ALTER TABLE `budget_view_where` ADD FOREIGN KEY (`budget_view_id`) REFERENCES budget_view(`id`)');
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("table_name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("field varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("budget_view_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_view_group_by', TRUE);
        
        $this->db->query('ALTER TABLE `budget_view_group_by` ADD FOREIGN KEY (`budget_view_id`) REFERENCES budget_view(`id`)');
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("table_name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("field varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("budget_view_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_view_filter', TRUE);
        
        $this->db->query('ALTER TABLE `budget_view_filter` ADD FOREIGN KEY (`budget_view_id`) REFERENCES budget_view(`id`)');
    }

    public function down() {
        $this->dbforge->drop_table('budget_view');
        $this->dbforge->drop_table('budget_view_field');
        $this->dbforge->drop_table('budget_view_join');
        $this->dbforge->drop_table('budget_view_group_by');
        $this->dbforge->drop_table('budget_view_where');
        $this->dbforge->drop_table('budget_view_field_color');
        $this->dbforge->drop_table('budget_view_filter');
    }

}
