<?php

class Rozpocet_pol_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }
    
    public function getRevenuesSum($subject, $year = NULL) {
        $this->db->select_sum('castka');
        $this->db->where('sekce_pol', 'P');
        $this->db->where('subjekt', $subject);
        if($year != NULL) {
            $this->db->where('rok', $year);
        }
        $query = $this->db->get('data_rozpocet_pol');
        return $query->row()->castka;
    }
    
    public function getCostsSum($subject, $year = NULL) {
        $this->db->select_sum('castka');
        $this->db->where('sekce_pol', 'V');
        $this->db->where('subjekt', $subject);
        if($year != NULL) {
            $this->db->where('rok', $year);
        }
        $query = $this->db->get('data_rozpocet_pol');
        return $query->row()->castka;
    }
  
    public function getRevenuesByItemClass($subject, $year = NULL) {
        $this->db->from('data_rozpocet_pol');
        $this->db->join('data_polozka_trida', 'data_rozpocet_pol.polozka_trida = data_polozka_trida.kod');
        if($year != NULL) {
            $this->db->where('data_rozpocet_pol.rok', $year);
        }
        $this->db->where('subjekt', $subject);
        $this->db->select('SUM(castka) AS castka, data_polozka_trida.nazev', FALSE);
        $this->db->where('sekce_pol', 'P');
        $this->db->group_by('polozka_trida');
        
        return $this->db->get()->result_array();
    }
    
    public function getCostsByParagraphGroup($subject, $year = NULL) {
        $this->db->from('data_rozpocet_pol');
        $this->db->join('data_paragraf_skupina', 'data_rozpocet_pol.paragraf_skupina = data_paragraf_skupina.kod');
        if($year != NULL) {
            $this->db->where('data_rozpocet_pol.rok', $year);
        }
        $this->db->where('subjekt', $subject);
        $this->db->select('SUM(castka) AS castka, data_paragraf_skupina.nazev', FALSE);
        $this->db->where('sekce_pol', 'V');
        $this->db->group_by('paragraf_skupina');
        
        return $this->db->get()->result_array();
    }
    
    public function getOverview($subject) {
        $this->db->select('SUM(castka) AS castka, sekce_pol, rok', FALSE);
        $this->db->where('subjekt', $subject);
        $this->db->group_by(array('sekce_pol','rok'));
        $query = $this->db->get('data_rozpocet_pol');
        return $query->result_array();
    }

}
