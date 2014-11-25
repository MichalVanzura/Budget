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
    
    public function getSimpleViewById($id, $subject = NULL, $year = NULL, $value = NULL) {
        $this->output->set_content_type('application/json');
        $result = $this->budget_view_model->getSimpleViewById($id, $subject, $year, $value);
        $this->output->set_output(json_encode($result));
    }
    
    public function getJoinViewById($id, $subject = NULL, $year = NULL, $value = NULL) {
        $this->output->set_content_type('application/json');
        $result = $this->budget_view_model->getJoinViewById($id, $subject, $year, $value);
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
        $chart = $this->input->post('chart');
        $filters = $this->input->post('filters');
        
        $firephp = FirePHP::getInstance(true);
        $firephp->log($chart, 'chart');

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

        $this->budget_view_model->createView($viewName, $distinctValue, $from, $joinArray, $fields, $whereArray, $groupby, $chart, $filters);
    }

    public function queryJoinViews() {
        $this->output->set_content_type('application/json');
        $views = $this->input->post('views');
        $joins = $this->input->post('joins');
        $fields = $this->input->post('fields');
        $aggregate = $this->input->post('aggregate');

        if (empty($fields)) {
            return;
        }

        $query = $this->budget_view_model->queryJoinView($views, $joins, $fields, $aggregate);

        $this->output->set_output(json_encode($query));
    }

    public function createJoinView() {
        $viewName = $this->input->post('name');
        $views = $this->input->post('views');
        $joins = $this->input->post('joins');
        $fields = $this->input->post('fields');
        $aggregate = $this->input->post('aggregate');
        $chart = $this->input->post('chart');

        $this->budget_view_model->createJoinView($viewName, $views, $joins, $fields, $aggregate, $chart);
    }

    public function getViewsTables() {
        $views = $this->input->post('views');

        $first = array_shift($views);
        $result = array();
        if ($first['category'] == 'simple') {
            $result = $this->budget_view_model->getSimpleViewTablesById($first['id']);
        } else {
            $result = $this->budget_view_model->getJoinViewTablesById($first['id']);
        }

        foreach ($views as $view) {
            if ($view['category'] == 'simple') {
                $tables = $this->budget_view_model->getSimpleViewTablesById($view['id']);
            } else {
                $tables = $this->budget_view_model->getJoinViewTablesById($view['id']);
            }

            foreach ($tables as $table) {
                array_push($result, $table);
            }
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode(array_keys(array_flip(($result)))));
    }

}
