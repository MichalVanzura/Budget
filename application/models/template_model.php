<?php

class Template_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function getHtmlTemplates() {
        return $this->db->get('html_template')->result_array();
    }

    public function getTemplates() {
        return $this->db->get('template')->result_array();
    }

    public function getHtmlTemplateFields($id) {
        return $this->db->get_where('html_template_field', array('html_template_id' => $id))->result_array();
    }

    public function getTemplateFields($id) {
        return $this->db->get_where('template_field', array('template_id' => $id))->result_array();
    }

    public function getHtmlTemplateById($id) {
        return $this->db->get_where('html_template', array('id' => $id))->row_array();
    }

    public function getTemplatesBySlug($slug) {
        $result['template'] = $this->db->get_where('template', array('slug' => $slug))->row_array();
        $result['htmlTemplate'] = $this->getHtmlTemplateById($result['template']['html_template_id']);
        $result['fields'] = array();

        $fields = $this->getTemplateFields($result['template']['id']);
        $i = 0;
        foreach ($fields as $field) {
            $result['fields'][$i]['field'] = $field;
            $result['fields'][$i]['html_field'] = $this->getHtmlTemplateFieldById($field['html_template_field_id']);
            $i++;
        }
        return $result;
    }

    public function getHtmlTemplateFieldById($id) {
        return $this->db->get_where('html_template_field', array('id' => $id))->row_array();
    }

    public function createTemplate($templateName, $slug, $htmlTemplateId, $fields, $template_view) {
        //LOG
        $firephp = FirePHP::getInstance(true);

        $template = array(
            'name' => $templateName,
            'slug' => $slug,
            'html_template_id' => $htmlTemplateId,
        );

        $this->db->insert('template', $template);
        $template_id = $this->db->insert_id();

        foreach ($fields as $field) {
            $template_field = array(
                'template_id' => $template_id,
                'display' => $field['display'],
                'html_template_field_id' => $field['id'],
            );

            if ($field['view']['category'] == 'simple') {
                $template_field['budget_view_id'] = $field['view']['id'];
            } else {
                $template_field['budget_join_view_id'] = $field['view']['id'];
            }

            $this->db->insert('template_field', $template_field);
        }

        if (!empty($template_view)) {

            $insert_view_template = array(
                'view_field_id' => $template_view['field']['id'],
                'template_id' => $template_id,
                'view_field_value' => $template_view['value'],
            );
            if ($template_view['view']['category'] == 'simple') {
                $insert_view_template['view_id'] = $template_view['view']['id'];
            } else {
                $insert_view_template['join_view_id'] = $template_view['view']['id'];
            }

            $this->db->insert('template_view', $insert_view_template);
        }

        $firephp->log($template_id, 'queryDB out');
    }

}
