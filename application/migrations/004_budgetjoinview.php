<?php

class Migration_BudgetJoinView extends CI_Migration {

    public function up() {
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("name varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("key_field varchar(255)");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_join_view', TRUE);
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("budget_join_view_id int(11) unsigned");
        $this->dbforge->add_field("first_view_id int(11) unsigned");
        $this->dbforge->add_field("second_view_id int(11) unsigned");
        $this->dbforge->add_field("first_view_field_id int(11) unsigned");
        $this->dbforge->add_field("second_view_field_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_view_join_join_view', TRUE);
        
        $this->db->query('ALTER TABLE `budget_view_join_join_view` ADD FOREIGN KEY (`budget_join_view_id`) REFERENCES budget_join_view(`id`)');
        $this->db->query('ALTER TABLE `budget_view_join_join_view` ADD FOREIGN KEY (`first_view_id`) REFERENCES budget_view(`id`)');
        $this->db->query('ALTER TABLE `budget_view_join_join_view` ADD FOREIGN KEY (`second_view_id`) REFERENCES budget_view(`id`)');
        $this->db->query('ALTER TABLE `budget_view_join_join_view` ADD FOREIGN KEY (`first_view_field_id`) REFERENCES budget_view_field(`id`)');
        $this->db->query('ALTER TABLE `budget_view_join_join_view` ADD FOREIGN KEY (`second_view_field_id`) REFERENCES budget_view_field(`id`)');
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("budget_field_id int(11) unsigned");
        $this->dbforge->add_field("budget_join_view_id int(11) unsigned");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('budget_field_join_join_view', TRUE);
        
        $this->db->query('ALTER TABLE `budget_field_join_join_view` ADD FOREIGN KEY (`budget_join_view_id`) REFERENCES budget_join_view(`id`)');
        $this->db->query('ALTER TABLE `budget_field_join_join_view` ADD FOREIGN KEY (`budget_field_id`) REFERENCES budget_view_field(`id`)');
        
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("budget_join_view_id int(11) unsigned");
        $this->dbforge->add_field("first_view_id int(11) unsigned");
        $this->dbforge->add_field("second_view_id int(11) unsigned");
        $this->dbforge->add_field("first_view_field_id int(11) unsigned");
        $this->dbforge->add_field("second_view_field_id int(11) unsigned");
        $this->dbforge->add_field("alias varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("formatter varchar(10)");
        $this->dbforge->add_field("operator varchar(10)");
        $this->dbforge->add_field("color varchar(7)");
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('aggregate', TRUE);
        
        $this->db->query('ALTER TABLE `aggregate` ADD FOREIGN KEY (`budget_join_view_id`) REFERENCES budget_join_view(`id`)');
        $this->db->query('ALTER TABLE `aggregate` ADD FOREIGN KEY (`first_view_id`) REFERENCES budget_view(`id`)');
        $this->db->query('ALTER TABLE `aggregate` ADD FOREIGN KEY (`second_view_id`) REFERENCES budget_view(`id`)');
        $this->db->query('ALTER TABLE `aggregate` ADD FOREIGN KEY (`first_view_field_id`) REFERENCES budget_view_field(`id`)');
        $this->db->query('ALTER TABLE `aggregate` ADD FOREIGN KEY (`second_view_field_id`) REFERENCES budget_view_field(`id`)');
    }

    public function down() {
        $this->dbforge->drop_table('budget_join_view');
        $this->dbforge->drop_table('budget_view_join_join_view');
        $this->dbforge->drop_table('budget_field_join_join_view');
        $this->dbforge->drop_table('aggregate');
    }

}
