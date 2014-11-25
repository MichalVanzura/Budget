<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class City extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('migration');
        $this->migration->current();
        $this->load->javascript('highcharts.js', false, false);
        $this->load->model('ucetnictvi_pol_model');
        $this->load->model('rozpocet_pol_model');
        $this->load->model('datainfo_model');
        $this->load->model('appearance_model');
    }

    private function getRevenuesByItemClass($subject, $year = NULL) {
        $query = $this->ucetnictvi_pol_model->getRevenuesByItemClass($subject, $year);
        $revenues = array();
        foreach ($query as $row) {
            $revenues[$row['nazev']] = $row['castka'];
        }
        return $revenues;
    }

    private function getCostsByParagraphGroup($subject, $year = NULL) {
        $query = $this->ucetnictvi_pol_model->getCostsByParagraphGroup($subject, $year);
        $costs = array();
        foreach ($query as $row) {
            $costs[$row['nazev']] = $row['castka'];
        }
        return $costs;
    }

    private function getBudgetRevenuesByItemClass($subject, $year = NULL) {
        $query = $this->rozpocet_pol_model->getRevenuesByItemClass($subject, $year);
        $revenues = array();
        foreach ($query as $row) {
            $revenues[$row['nazev']] = $row['castka'];
        }
        return $revenues;
    }

    private function getBudgetCostsByParagraphGroup($subject, $year = NULL) {
        $query = $this->rozpocet_pol_model->getCostsByParagraphGroup($subject, $year);
        $costs = array();
        foreach ($query as $row) {
            $costs[$row['nazev']] = $row['castka'];
        }
        return $costs;
    }

    private function getOverviewChart($subject) {
        $overviewChart = array(
            'Příjmy' => array(),
            'Výdaje' => array(),
            'Saldo' => array(),
        );

        $overview = $this->ucetnictvi_pol_model->getOverview($subject);
        foreach ($overview as $result) {
            if ($result['sekce_pol'] == 'P') {
                $overviewChart['Příjmy'][$result['rok']] = $result['castka'];
            } elseif ($result['sekce_pol'] == 'V') {
                $overviewChart['Výdaje'][$result['rok']] = $result['castka'];
            }
        }

        foreach ($overviewChart['Příjmy'] as $item => $value) {
            $overviewChart['Saldo'][$item] = $overviewChart['Příjmy'][$item] - $overviewChart['Výdaje'][$item];
            $overviewChart['Kategorie'][] = $item;
        }

        return $overviewChart;
    }

    private function createData($subject, $year, $datainfo) {
        $revenues = $this->getRevenuesByItemClass($subject, $year);
        $revenuesBudget = array_map(function ($x, $y) {
            return $x - $y;
        }, $this->getBudgetRevenuesByItemClass($subject, $year), $revenues);

        $costs = $this->getRevenuesByItemClass($subject, $year);
        $costsBudget = array_map(function ($x, $y) {
            return $x - $y;
        }, $this->getBudgetCostsByParagraphGroup($subject, $year), $costs);

        $data = array(
            'datainfo' => $datainfo,
            'appearance' => $this->appearance_model->get_appearance($subject),
            'revenuesChart' => $this->getRevenuesByItemClass($subject, $year),
            'costsChart' => $this->getCostsByParagraphGroup($subject, $year),
            'revenuesBudget' => $revenuesBudget,
            'costsBudget' => $costsBudget,
            'revenuesSum' => $this->ucetnictvi_pol_model->getRevenuesSum($subject, $year),
            'costsSum' => $this->ucetnictvi_pol_model->getCostsSum($subject, $year),
            'overviewChart' => $this->getOverviewChart($subject),
        );

        return $data;
    }

    public function view($subject, $year) {
        $datainfo = $this->datainfo_model->getDataInfoBySubjectAndYear($subject, $year);
        $this->load->title($datainfo['subjekt_nazev'] . ' | ' . $year);

        $data = $this->createData($subject, $year, $datainfo);
        $data['subMenu'] = true;
        if ($year != date("Y")) {
            $this->load->template('city/view_old', $data);
        } else {
            $this->load->template('city/view_new', $data);
        }
        
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
            $data['budget'][$row['rok']] = $this->createData($subject, $row['rok'], $row);
        }

        $this->load->template('city/index', $data);
    }

    public function revenues($subject, $year = NULL) {
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subject);
        $this->load->title($datainfo[0]['subjekt_nazev'] . ' ' . $year . ' | ' . 'Příjmy');

        $data = array(
            'datainfo' => $datainfo[0],
            'prefix' => 'prijmy/',
            'next_level' => 'polozka/mesto/' . $subject . '/' . $year . '/',
            'table' => $this->ucetnictvi_pol_model->getRevenuesByItemClass($subject, $year),
        );
        $this->load->template('table', $data);
    }

    public function costs($subject, $year = NULL) {
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subject);
        $this->load->title($datainfo[0]['subjekt_nazev'] . ' ' . $year . ' | ' . 'Příjmy');

        $data = array(
            'datainfo' => $datainfo[0],
            'prefix' => 'vydaje/',
            'next_level' => 'paragraf/mesto/' . $subject . '/' . $year . '/',
            'table' => $this->ucetnictvi_pol_model->getCostsByParagraphGroup($subject, $year),
        );
        $this->load->template('table', $data);
    }

    public function itemClass($subject, $itemClass, $year = NULL) {
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subject);

        $data = array(
            'datainfo' => $datainfo[0],
            'prefix' => 'polozka/',
            'table' => $this->ucetnictvi_pol_model->itemClass($subject, $itemClass, $year),
        );
        $this->load->template('table', $data);
    }

    public function paragraphGroup($subject, $paragraphGroup, $year = NULL) {
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subject);

        $data = array(
            'datainfo' => $datainfo[0],
            'prefix' => 'paragraf/',
            'table' => $this->ucetnictvi_pol_model->paragraphGroup($subject, $paragraphGroup, $year),
        );
        $this->load->template('table', $data);
    }

}
