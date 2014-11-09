<?php

class Dynamic_Menu {

    private $ci;

    public function __construct() {
        $this->ci = & get_instance();
    }
    
    private function get_children($subject) {
        $this->ci->db->from('menu');
        $this->ci->db->join('data_datainfo', 'menu.subject = data_datainfo.subjekt');
        $this->ci->db->where('nadrazenysubjekt', $subject);
        $this->ci->db->where('subjekt !=', $subject);
        $query = $this->ci->db->get()->result_array();
        
        $children = array();
        foreach ($query as $child) {
            $children[$child['subject']] = array(
                'title' => $child['title'],
                'url' => $child['url'],
            );
        }
        return $children;
    }

    function build_subject_menu($subject = NULL) {
        if($subject === NULL) {
            return;
        }
        $this->ci->db->from('menu');
        $this->ci->db->join('data_datainfo', 'menu.subject = data_datainfo.subjekt');
        $this->ci->db->where('subjekt', $subject);
        $this->ci->db->group_by("subjekt");

        $query = $this->ci->db->get()->row_array();

        $menu = array();
        if ($query['subjekt_typ'] == 0) {
            $menu['parent'] = array(
                'title' => $query['title'],
                'url' => $query['url'],
            );
            $menu['children'] = $this->get_children($query['subject']);
        } else {
            $this->ci->db->where('subject', $query['parent_subject']);
            $query = $this->ci->db->get('menu')->row_array();
            $menu['parent'] = array(
                'title' => $query['title'],
                'url' => $query['url'],
            );
            $menu['children'] = $this->get_children($query['subject']);
        }

        return $menu;
    }
    
    function build_years_menu($subject = NULL) {
        if($subject === NULL) {
            return;
        }
        $this->ci->db->select('data_datainfo.rok, menu.url');
        $this->ci->db->from('menu');
        $this->ci->db->join('data_datainfo', 'menu.subject = data_datainfo.subjekt');
        $this->ci->db->where('subjekt', $subject);
        $this->ci->db->order_by("data_datainfo.rok"); 

        $query = $this->ci->db->get()->result_array();
        return $query;
    }
}
