<?php

class budgetView extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('budget_view_model');
    }

    public function getViews() {
        $this->output->set_content_type('application/json');
        $result = $this->budget_view_model->getViews();
        $this->output->set_output(json_encode($result));
    }

    public function getViewFields() {
        $this->output->set_content_type('application/json');
        $result = $this->budget_view_model->getViewFields();
        $this->output->set_output(json_encode($result));
    }
    
    public function getAllViews() {
        $this->output->set_content_type('application/json');
        $result = $this->budget_view_model->getAllViews();
        $this->output->set_output(json_encode($result));
    }

    public function createView() {
        $viewName = $this->input->post('viewName');
        $tables = $this->input->post('tables');
        $joins = $this->input->post('joins');
        $distinct = $this->input->post('distinct');
        $fields = $this->input->post('fields');
        $where = $this->input->post('where');
        $groupby = $this->input->post('groupby');
        $chartStacking = $this->input->post('chartStacking');
        $chartCategory = $this->input->post('chartCategory');
        $chartFields = $this->input->post('chartFields');

        $distinctValue = FALSE;
        if ($distinct === 'true') {
            $distinctValue = TRUE;
        }

        $whereArray = array();
        if ($where["0"]["table_name"] != "") {
            $whereArray = $where;
        }

        $joinArray = array();
        if ($joins != NULL) {
            for ($i = 0; $i < count($tables) - 1; $i++) {
                $joinArray[$i] = array(
                    'first_table' => $tables[$i],
                    'second_table' => $tables[$i + 1],
                    'first_field' => $joins[$i][0],
                    'second_field' => $joins[$i][1],
                );
            }
        }
        $from = array_shift($tables);

        $this->budget_view_model->createView($viewName, $distinctValue, $from, $joinArray, $fields, $whereArray, $groupby, $chartStacking, $chartCategory, $chartFields);
    }

    public function queryJoinViews() {
        $this->output->set_content_type('application/json');
        $views = $this->input->post('views');
        $joins = $this->input->post('joins');
        $fields = $this->input->post('fields');
        
        if(empty($fields)) {
            return;
        }

        //LOG
        $firephp = FirePHP::getInstance(true);

        $i = 0;
        $from = '';
        $previousId = 0;
        $joinsArray = array();
        foreach ($views as $view) {
            $viewSQL = $this->budget_view_model->getViewSQL($view['id']);
            if ($i == 0) {
                $from = sprintf("(%s) AS `view%d`", $viewSQL, $view['id']);
            } else {
                $joinstr = sprintf("LEFT JOIN (%s) AS `view%d` ON `view%d`.`%s`=`view%d`.`%s`", $viewSQL, $view['id'], $previousId, $joins[$i-1][0]['alias'], $view['id'], $joins[$i-1][1]['alias']);
                array_push($joinsArray, $joinstr);
            }
            $previousId = $view['id'];
            $i++;
        }
        
        $selectArray = array();
        foreach($fields as $field) {
            $selectstr = sprintf("`view%s`.`%s`", $field['budget_view_id'], $field['alias']);
            array_push($selectArray, $selectstr);
        }
        

        $sql = sprintf("SELECT %s FROM %s %s", implode(', ', $selectArray), $from, implode(' ', $joinsArray));
        $query = $this->db->query($sql)->result_array();
        $firephp->log($query, 'sql');

        $this->output->set_output(json_encode($query));
    }
    
    public function createJoinView() {
        $viewName = $this->input->post('name');
        $views = $this->input->post('views');
        $joins = $this->input->post('joins');
        $fields = $this->input->post('fields');
        
        $this->budget_view_model->createJoinView($viewName, $views, $joins, $fields);
    }

}
