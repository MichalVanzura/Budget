<?php

class Datainfo_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function getDataInfos() {
        $this->db->where('subjekt_typ', 0);
        $this->db->group_by('subjekt');
        $query = $this->db->get('data_datainfo');
        return $query->result_array();
    }
    
    public function getDataInfoBySubject($subject) {
        $this->db->from('data_datainfo');
        $this->db->join('menu', 'data_datainfo.subjekt = menu.subject');
        $this->db->where('subjekt', $subject);
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    public function getDataInfoBySubjectAndYear($subject, $year) {
        $this->db->from('data_datainfo');
        $this->db->join('menu', 'data_datainfo.subjekt = menu.subject');
        $this->db->where('subjekt', $subject);
        $this->db->where('rok', $year);
        $query = $this->db->get();
        
        return $query->row_array();
    }
}
