<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function index() {
//    	$this->load->model('datainfo_model');
//    	$data = array(
//    		'infos' => $this->datainfo_model->getDataInfos(),
//    	);
//        $this->load->view('welcome', $data);
        redirect(base_url().'mesto/00283347/2014','refresh');
    }
}
