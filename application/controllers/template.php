<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Template extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('template_model');
    }

    public function getHtmlTemplates() {
        $this->output->set_content_type('application/json');
        $result = $this->template_model->getHtmlTemplates();
        $this->output->set_output(json_encode($result));
    }

    public function getHtmlTemplateFields($id) {
        $this->output->set_content_type('application/json');
        $result = $this->template_model->getHtmlTemplateFields($id);
        $this->output->set_output(json_encode($result));
    }

    public function createTemplate() {
        $templateName = $this->input->post('name');
        $slug = $this->input->post('slug');
        $htmlTemplateId = $this->input->post('htmlTemplateId');
        $fields = $this->input->post('fields');

        $this->template_model->createTemplate($templateName, $slug, $htmlTemplateId, $fields);
    }

}
