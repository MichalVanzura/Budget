<?php


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Org extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->javascript('highcharts.js', false, false);
        $this->load->model('ucetnictvi_nv_model');
        $this->load->model('datainfo_model');
        $this->load->model('appearance_model');
    }
    
    private function getRevenueBySGroup($subject, $year = NULL) {
        $query = $this->ucetnictvi_nv_model->getRevenuesBySGroup($subject, $year);
        $revenues = array();
        foreach ($query as $row) {
            $revenues[$row['nazev']] = $row['castka'];
        }
        return $revenues;
    } 
    
    private function getCostsBySGroup($subject, $year = NULL) {
        $query = $this->ucetnictvi_nv_model->getCostsBySGroup($subject, $year);
        $costs = array();
        foreach ($query as $row) {
            $costs[$row['nazev']] = $row['castka'];
        }
        return $costs;
    } 
    
    public function view($subject, $year) {    
        $datainfo = $this->datainfo_model->getDataInfoBySubjectAndYear($subject, $year);
        $this->load->title($datainfo['subjekt_nazev'].' | '.$year);
        
        $data = array (
            'datainfo' => $datainfo,
            'appearance' => $this->appearance_model->get_appearance($subject),
            'revenuesChart' => $this->getRevenueBySGroup($subject, $year),
            'costsChart' => $this->getCostsBySGroup($subject, $year),
            'revenuesSum' => $this->ucetnictvi_nv_model->getRevenuesSum($subject, $year),
            'costsSum' => $this->ucetnictvi_nv_model->getCostsSum($subject, $year),
            'subMenu' => true,
        );
        $this->load->template('org/view', $data);
    }
    
    public function index($subject) {    
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subject);
        
        $data = array(
            'datainfo' => $datainfo[0],
            'appearance' => $this->appearance_model->get_appearance($subject),
            'subMenu' => true,
        );
        $this->load->title($datainfo[0]['subjekt_nazev']);
        
        foreach ($datainfo as $row) {
            $data['budget'][$row['rok']] = array(
                'datainfo' => $row,
                'revenuesChart' => $this->getRevenueBySGroup($subject, $row['rok']),
                'costsChart' => $this->getCostsBySGroup($subject, $row['rok']),
                'revenuesSum' => $this->ucetnictvi_nv_model->getRevenuesSum($subject, $row['rok']),
                'costsSum' => $this->ucetnictvi_nv_model->getCostsSum($subject, $row['rok']),
            );
        }
        
        $this->load->template('org/index', $data);  
    }
    
    public function revenues($subject, $year = NULL) {
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subject);
        $this->load->title($datainfo[0]['subjekt_nazev'] . ' ' . $year . ' | ' . 'Příjmy');
        
        $data = array(
            'datainfo' => $datainfo[0],
            'prefix' => 'vynosy/',
            'table' => $this->ucetnictvi_nv_model->getRevenuesBySGroup($subject, $year),
        );
        $this->load->template('table', $data);
    }
    
    public function costs($subject, $year = NULL) {
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subject);
        $this->load->title($datainfo[0]['subjekt_nazev'] . ' ' . $year . ' | ' . 'Příjmy');
        
        $data = array(
            'datainfo' => $datainfo[0],
            'prefix' => 'naklady/',
            'table' => $this->ucetnictvi_nv_model->getCostsBySGroup($subject, $year),
        );
        $this->load->template('table', $data);
    }
}

