<?php

class Appearance_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function set($data) {
        $this->db->from('appearance')->where('subject', $data['subject']);
        if ($this->db->count_all_results() == 0) { 
            return $this->db->insert('appearance', $data);
        } else {
            $this->db->where('subject', $data['subject']);
            return $this->db->update('appearance', $data);
        }
    }
    
    public function get_appearance($subject) {
        $this->db->where('subject', $subject);
        $result = $this->db->get('appearance');
        return $result->row_array();
    }
}
