<?php

class Ucetnictvi_nv_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }
    
    public function getRevenuesSum($subject, $year = NULL) {
        $this->db->select_sum('castka');
        $this->db->where('sekce_nv', 'V');
        $this->db->where('subjekt', $subject);
        if($year != NULL) {
            $this->db->where('rok', $year);
        }
        $query = $this->db->get('data_ucetnictvi_nv');
        return $query->row()->castka;
    }
    
    public function getCostsSum($subject, $year = NULL) {
        $this->db->select_sum('castka');
        $this->db->where('sekce_nv', 'N');
        $this->db->where('subjekt', $subject);
        if($year != NULL) {
            $this->db->where('rok', $year);
        }
        $query = $this->db->get('data_ucetnictvi_nv');
        return $query->row()->castka;
    }
  
    public function getRevenuesBySGroup($subject, $year = NULL) {
        $this->db->select('data_ucet_s_skupina.kod AS kod, data_ucet_s_skupina.nazev, SUM(castka) AS castka', FALSE);
        $this->db->from('data_ucetnictvi_nv');
        $this->db->join('data_ucet_s_skupina', 'data_ucetnictvi_nv.ucet_s_skupina = data_ucet_s_skupina.kod');
        if($year != NULL) {
            $this->db->where('data_ucetnictvi_nv.rok', $year);
        }
        $this->db->where('subjekt', $subject);
        $this->db->where('sekce_nv', 'V');
        $this->db->order_by("data_ucet_s_skupina.kod");
        $this->db->group_by('ucet_s_skupina');
        
        return $this->db->get()->result_array();
    }
    
    public function getCostsBySGroup($subject, $year = NULL) {
        $this->db->select('data_ucet_s_skupina.kod, data_ucet_s_skupina.nazev, SUM(castka) AS castka', FALSE);
        $this->db->from('data_ucetnictvi_nv');
        $this->db->join('data_ucet_s_skupina', 'data_ucetnictvi_nv.ucet_s_skupina = data_ucet_s_skupina.kod');
        if($year != NULL) {
            $this->db->where('data_ucetnictvi_nv.rok', $year);
        }
        $this->db->where('subjekt', $subject);
        $this->db->where('sekce_nv', 'N');
        $this->db->order_by("data_ucet_s_skupina.kod");
        $this->db->group_by('ucet_s_skupina');
        
        return $this->db->get()->result_array();
    }

}
