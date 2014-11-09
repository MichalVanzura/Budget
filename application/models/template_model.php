<?php
class Template_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function getHtmlTemplates() {
        return $this->db->get('html_template')->result_array();
    }
    
    public function getHtmlTemplateFields($id) {
        return $this->db->get_where('html_template_field', array('html_template_id' => $id))->result_array();
    }
    
    public function createTemplate($templateName, $slug, $htmlTemplateId, $fields) {
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
            
            if($field['view']['category'] == 'simple') {
                $template_field['budget_view_id'] = $field['view']['id'];
            } else {
                $template_field['budget_join_view_id'] = $field['view']['id'];
            }
            
            $this->db->insert('template_field', $template_field);
        }
        
        $firephp->log($template_id, 'queryDB out');
    }
}

