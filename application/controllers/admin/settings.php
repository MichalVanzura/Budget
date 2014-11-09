<?php

class Settings extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('datainfo_model');
    }

    public function index($subjekt) {
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subjekt);
        $data = array(
            'datainfo' => $datainfo[0],
        );
        $this->load->admintemplate('admin/settings', $data);
    }
    
    public function defineView($subjekt) {
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subjekt);
        $data = array(
            'datainfo' => $datainfo[0],
        );
        $this->load->admintemplate('admin/define_view/define_view', $data);
    }
}
