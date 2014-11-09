<?php

class Ucetnictvi_pol_model extends CI_Model {

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
        $query = $this->db->get('data_ucetnictvi_pol');
        return $query->row()->castka;
    }
    
    public function getCostsSum($subject, $year = NULL) {
        $this->db->select_sum('castka');
        $this->db->where('sekce_pol', 'V');
        $this->db->where('subjekt', $subject);
        if($year != NULL) {
            $this->db->where('rok', $year);
        }
        $query = $this->db->get('data_ucetnictvi_pol');
        return $query->row()->castka;
    }
  
    public function getRevenuesByItemClass($subject, $year = NULL) {
        $this->db->select('data_polozka_trida.kod AS kod, data_polozka_trida.nazev, SUM(castka) AS castka', FALSE);
        $this->db->from('data_ucetnictvi_pol');
        $this->db->join('data_polozka_trida', 'data_ucetnictvi_pol.polozka_trida = data_polozka_trida.kod');
        if($year != NULL) {
            $this->db->where('data_ucetnictvi_pol.rok', $year);
        }
        $this->db->where('subjekt', $subject);
        $this->db->where('sekce_pol', 'P');
        $this->db->order_by("data_polozka_trida.kod");
        $this->db->group_by('polozka_trida');

        $result = $this->db->get();        
        return $result->result_array();
    }
    
    public function getCostsByParagraphGroup($subject, $year = NULL) {
        $this->db->select('data_paragraf_skupina.kod AS kod, data_paragraf_skupina.nazev, SUM(castka) AS castka', FALSE);
        $this->db->from('data_ucetnictvi_pol');
        $this->db->join('data_paragraf_skupina', 'data_ucetnictvi_pol.paragraf_skupina = data_paragraf_skupina.kod');
        if($year != NULL) {
            $this->db->where('data_ucetnictvi_pol.rok', $year);
        }
        $this->db->where('subjekt', $subject);
        $this->db->where('sekce_pol', 'V');
        $this->db->order_by("data_paragraf_skupina.kod");
        $this->db->group_by('paragraf_skupina');
        
        return $this->db->get()->result_array();
    }
    
    public function getOverview($subject) {
        $this->db->select('SUM(castka) AS castka, sekce_pol, rok', FALSE);
        $this->db->where('subjekt', $subject);
        $this->db->group_by(array('sekce_pol','rok'));
        $query = $this->db->get('data_ucetnictvi_pol');
        return $query->result_array();
    }
    
    public function itemClass($subject, $itemClass, $year = NULL) {
        $this->db->select('data_polozka.kod, data_polozka.nazev, castka');
        $this->db->from('data_ucetnictvi_pol');
        $this->db->join('data_polozka', 'data_ucetnictvi_pol.polozka = data_polozka.kod');
        if($year != NULL) {
            $this->db->where('data_ucetnictvi_pol.rok', $year);
        }
        $this->db->where('subjekt', $subject);
        $this->db->where('polozka_trida', $itemClass);
        $this->db->order_by("data_polozka.kod");
        $this->db->group_by("data_polozka.kod");
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function paragraphGroup($subject, $paragraphGroup, $year = NULL) {
        $this->db->select('data_paragraf.kod, data_paragraf.nazev, castka');
        $this->db->from('data_ucetnictvi_pol');
        $this->db->join('data_paragraf', 'data_ucetnictvi_pol.paragraf = data_paragraf.kod');
        if($year != NULL) {
            $this->db->where('data_ucetnictvi_pol.rok', $year);
        }
        $this->db->where('subjekt', $subject);
        $this->db->where('paragraf_skupina', $paragraphGroup);
        $this->db->order_by("data_paragraf.kod");
        $this->db->group_by("data_paragraf.kod");
        $query = $this->db->get();
        return $query->result_array();
    }

}
