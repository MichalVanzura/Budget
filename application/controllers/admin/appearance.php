<?php

class Appearance extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('appearance_model');
        $this->load->model('datainfo_model');
    }

    public function logo($subjekt) {
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subjekt);
        $this->load->title('Logo'.' | '.$datainfo[0]['subjekt_nazev']);
        $data = array(
            'datainfo' => $datainfo[0],
            'appearance' => $this->appearance_model->get_appearance($subjekt),
            'hidden' => array('subjekt' => $subjekt),
        );
        $this->load->admintemplate('admin/appearance/logo-upload', $data);
    }

    public function logoUpload() {
        $this->form_validation->set_rules('userfile', 'Logo', 'trim|xss_clean');

        $subjekt = $this->input->post('subjekt');
        $datainfo = $this->datainfo_model->getDataInfoBySubject($subjekt);
        $this->load->title('Nahrání loga'.' | '.$datainfo[0]['subjekt_nazev']);
        $data = array(
            'datainfo' => $datainfo[0],
            'appearance' => $this->appearance_model->get_appearance($subjekt),
            'hidden' => array('subjekt' => $subjekt),
        );

        if ($this->form_validation->run() === FALSE) {
            if (validation_errors() != '') {
                $this->load->addAlert(validation_errors(), ALERT_DANGER);
            }
            $this->load->admintemplate('admin/appearance/logo-upload', $data);
        } else {
            $config['upload_path'] = './assets/img/logo/';
            $config['allowed_types'] = 'gif|jpg|png';

            $this->load->library('upload', $config);

            //File Upload
            if (!$this->upload->do_upload()) {
                $this->load->addAlert($this->upload->display_errors(), ALERT_DANGER);
                $this->load->template('admin/appearance/logo-upload', $data);
            } else {

                $upload_data = $this->upload->data();
                $filePath = 'assets/img/logo/' . $upload_data['client_name'];

                $appearanceData = array(
                    'subject' => $subjekt,
                    'logo_path' => $filePath,
                );

                $this->appearance_model->set($appearanceData);
                
                //unlink($data['appearance']['logo_path']);
                
                $this->load->addAlert('Nastavení úspěšně změněno', ALERT_SUCCESS);
                $this->load->admintemplate('default', $data);
            }
        }
    }

    public function colors($subjekt = NULL) {
        $this->load->javascript('jquery.minicolors.js', false, false);
        $this->load->css('jquery.minicolors.css');

        if($subjekt === NULL) {
            $subjekt = $this->input->post('subjekt');
        }
        $this->form_validation->set_rules('headercolor', 'Barva hlavičky', 'trim|required|xss_clean');

        $datainfo = $this->datainfo_model->getDataInfoBySubject($subjekt);
        $this->load->title('Vzhled'.' | '.$datainfo[0]['subjekt_nazev']);
        $data = array(
            'datainfo' => $datainfo[0],
            'appearance' => $this->appearance_model->get_appearance($subjekt),
            'hidden' => array('subjekt' => $subjekt),
        );

        if ($this->form_validation->run() == FALSE) {
            if (validation_errors() != '') {
                $this->load->addAlert(validation_errors(), ALERT_DANGER);
            }
            $this->load->admintemplate('admin/appearance/colors', $data);
        } else {
            $appearanceData = array(
                'subject' => $subjekt,
                'header_color' => $this->input->post('headercolor'),
            );

            $this->appearance_model->set($appearanceData);
            $this->load->addAlert('Nastavení úspěšně změněno', ALERT_SUCCESS);
            $this->load->admintemplate('default', $data);
        }
    }
}
