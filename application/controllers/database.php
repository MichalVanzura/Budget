<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Database extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('database_model');
        $this->output->set_content_type('application/json');
    }

    public function getTablesAndFields() {
        $result = $this->database_model->getTableNames();
        $values = array();
        foreach ($result as $value) {
            foreach ($value as $item) {
                $fields = $this->database_model->getTableFields($item);
                $values[$item] = $fields;
            }
        }
        $this->output->set_output(json_encode($values));
    }

    public function getTableInfos() {
        $result = $this->database_model->getTableNames();
        $values = array();
        foreach ($result as $value) {
            foreach ($value as $item) {
                $fields = $this->database_model->getTableFields($item);
                foreach ($fields as $field) {
                    $values[$item][$item . '.' . $field] = array(
                        'table_name' => $item,
                        'name' => $field,
                        'alias' => $item . '.' . $field,
                        'function' => 'standard',
                        'formatter' => 'no',
                    );
                }
            }
        }
        $this->output->set_output(json_encode($values));
    }

    public function getTableFields($tableName) {
        $result = $this->database_model->getTableFields($tableName);
        $output = array();
        foreach ($result as $row) {
            $output[$row] = array(
                'key' => $row,
                'name' => $row,
            );
        }
        $this->output->set_output(json_encode($output));
    }

    public function queryDatabase() {
        $tables = $this->input->post('tables');
        $joins = $this->input->post('joins');
        $distinct = $this->input->post('distinct');
        $fields = $this->input->post('fields');
        $where = $this->input->post('where');
        $limit = $this->input->post('limit');
        $groupby = $this->input->post('groupby');

        //LOG
        $firephp = FirePHP::getInstance(true);
//        $firephp->log($groupby, 'groupby');

        if ($tables == NULL) {
            return;
        }
        if ($fields == NULL) {
            return;
        }

        $whereArray = array();
        if ($where["0"]["table_name"] != "") {
            $whereArray = $where;
        }

        $fieldArray = array();
        foreach ($fields as $field) {
            $fieldstring = sprintf("%s.%s AS '%s'", $field['table_name'], $field['name'], $field['alias']);
            if($field['function'] == 'sum') {
                $fieldstring = sprintf("SUM(%s.%s) AS '%s'", $field['table_name'], $field['name'], $field['alias']);
            }
            array_push($fieldArray, $fieldstring);
        }
        
//        $withRU = FALSE;
//        if($withrollup['rollup'] === 'true') {
//            $withRU = TRUE;
//            $fieldstring = sprintf("COALESCE(%s,'%s')", $withrollup['field_alias'], $withrollup['name']);
//            array_push($fieldArray, $fieldstring);
//        }

        $select = implode(",", $fieldArray);

        $joinArray = array();
        if ($joins != NULL) {
            for ($i = 0; $i < count($tables) - 1; $i++) {
                $joinstring = sprintf("%s.%s=%s.%s", $tables[$i], $joins[$i][0], $tables[$i + 1], $joins[$i][1]);
                $joinArray[$i] = array(
                    $tables[$i + 1] => $joinstring,
                );
            }
        }
        $from = array_shift($tables);

        $result = $this->database_model->queryDatabase($select, $from, $joinArray, $distinct, $whereArray, $limit, $groupby);
        $firephp->log($result, 'result');
        $this->output->set_output(json_encode($result));
    }

}
