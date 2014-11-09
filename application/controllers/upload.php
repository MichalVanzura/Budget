<?php

class Upload extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form'));
        $this->load->library('unzip');
        $this->unzip->allow(array('xml'));
        $this->load->title('Nahrát soubor');
    }

    function index() {
        $this->load->template('upload_form');
    }

    function do_upload() {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'zip';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            $this->load->addAlert($this->upload->display_errors(), ALERT_DANGER);

            $this->load->template('upload_form');
        } else {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];
            $fileInfo = array(
                'upload_data' => $upload_data,
            );
            $file_path = 'uploads/' . $file_name;
            $this->unzip->extract($file_path, 'uploads/queue');
            unlink($file_path);

            $this->load->template('upload_success', $fileInfo);
        }
    }

}

?>