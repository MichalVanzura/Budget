<?php

class Budget_view_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function getViews() {
        return $this->db->get('budget_view')->result_array();
    }

    public function getViewFields() {
        return $this->db->get('budget_view_field')->result_array();
    }

    public function getAllViews() {
        $views = $this->db->get('budget_view')->result_array();
        $viewsArray = array();
        foreach ($views as $view) {
            $view['category'] = 'simple';
            array_push($viewsArray, $view);
        }
        $joinsArray = array();
        $joins = $this->db->get('budget_join_view')->result_array();
        foreach ($joins as $join) {
            $join['category'] = 'join';
            array_push($joinsArray, $join);
        }
        return array_merge($viewsArray, $joinsArray);
    }

    public function getViewSQL($id) {
        //LOG
        $firephp = FirePHP::getInstance(true);

        $view = $this->db->get_where('budget_view', array('id' => $id))->row_array();

        $fields = $this->db->get_where('budget_view_field', array('budget_view_id' => $id))->result_array();
        $selectArray = array();
        foreach ($fields as $field) {
            $function = '';
            if ($field['function'] != 'standard') {
                $function = strtoupper($field['function']);
            }
            $select = sprintf("%s(`%s`.`%s`) AS '%s'", $function, $field['table_name'], $field['name'], $field['alias']);
            array_push($selectArray, $select);
        }


        $joins = $this->db->get_where('budget_view_join', array('budget_view_id' => $id))->result_array();
        $joinsArray = array();
        foreach ($joins as $join) {
            $joinstr = sprintf("JOIN `%s` ON `%s`.`%s`=`%s`.`%s`", $join['second_table'], $join['first_table'], $join['first_field'], $join['second_table'], $join['second_field']);
            array_push($joinsArray, $joinstr);
        }

        $where = $this->db->get_where('budget_view_where', array('budget_view_id' => $id))->result_array();
        $whereArray = array();
        for ($i = 0; $i < count($where); $i++) {
            $wherestr = '';
            if ($i == 0) {
                $wherestr = sprintf("`%s`.`%s`%s'%s'", $where[$i]['table_name'], $where[$i]['field'], $where[$i]['operator'], $where[$i]['value']);
            } else {
                $wherestr = sprintf("%s `%s`.`%s`%s'%s'", $where[$i]['rel'], $where[$i]['table_name'], $where[$i]['field'], $where[$i]['operator'], $where[$i]['value']);
            }
            array_push($whereArray, $wherestr);
        }

        $groupby = $this->db->get_where('budget_view_group_by', array('budget_view_id' => $id))->result_array();
        $groupByArray = array();
        foreach ($groupby as $gb) {
            $groupbystr = sprintf("`%s`.`%s`", $gb['table_name'], $gb['field']);
            array_push($groupByArray, $groupbystr);
        }

        $selectstr = '*';
        if (!empty($selectArray)) {
            $selectstr = implode(', ', $selectArray);
        }

        $wherestr = '';
        if (!empty($whereArray)) {
            $wherestr = 'WHERE ' . implode(' ', $whereArray);
        }

        $groupbystr = '';
        if (!empty($groupByArray)) {
            $groupbystr = 'GROUP BY ' . implode(', ', $groupByArray);
        }

        $rawsql = sprintf(
                "SELECT %s FROM `%s` %s %s %s", $selectstr, $view['from_table'], implode(' ', $joinsArray), $wherestr, $groupbystr);

        return $rawsql;
    }

    public function createView($viewName, $distinct, $from, $joins, $fields, $where, $groupby, $chartStacking, $chartCategory, $chartFields) {
        //LOG
        $firephp = FirePHP::getInstance(true);

        $budget_view = array(
            'name' => $viewName,
            'distinct_rows' => $distinct,
            'from_table' => $from,
        );

        $this->db->insert('budget_view', $budget_view);
        $budget_view_id = $this->db->insert_id();

        $budget_view_joins = $joins;
        for ($i = 0; $i < count($joins); $i++) {
            $budget_view_joins[$i]['budget_view_id'] = $budget_view_id;
            $this->db->insert('budget_view_join', $budget_view_joins[$i]);
        }

        foreach ($fields as $field) {
            $field['budget_view_id'] = $budget_view_id;
            $this->db->insert('budget_view_field', $field);
        }

        foreach ($where as $w) {
            $w['budget_view_id'] = $budget_view_id;
            $this->db->insert('budget_view_where', $w);
        }

        foreach ($groupby as $gb) {
            $items = explode('.', $gb);
            $budget_view_group_by = array(
                'table_name' => $items[0],
                'field' => $items[1],
                'budget_view_id' => $budget_view_id,
            );
            $this->db->insert('budget_view_group_by', $budget_view_group_by);
        }

        $chart = array(
            'view_id' => $budget_view_id,
            'view_type' => 'simple',
            'stacking' => $chartStacking,
        );
        $this->db->insert('chart', $chart);
        $chart_id = $this->db->insert_id();

        $fieldDB = $this->db->get_where('budget_view_field', $chartCategory)->row_array();
        $category = array(
            'field_id' => $fieldDB['id'],
            'chart_id' => $chart_id,
        );
        $this->db->insert('chart_category', $category);

        foreach ($chartFields as $chartField) {
            $fieldDB = $this->db->get_where('budget_view_field', $chartField)->row_array();
            $serie = array(
                'field_id' => $fieldDB['id'],
                'chart_id' => $chart_id,
            );
            $this->db->insert('chart_serie', $serie);
        }


        $firephp->log($budget_view_id, 'queryDB out');
    }

    public function createJoinView($viewName, $views, $joins, $fields) {
        //LOG
        $firephp = FirePHP::getInstance(true);

        $budget_join_view = array(
            'name' => $viewName,
        );

        $this->db->insert('budget_join_view', $budget_join_view);
        $budget_join_view_id = $this->db->insert_id();

        for ($i = 0; $i < count($joins); $i++) {
            $budget_view_join_join_view = array(
                'budget_join_view_id' => $budget_join_view_id,
                'first_view_id' => $views[$i]['id'],
                'second_view_id' => $views[$i + 1]['id'],
                'first_view_field_id' => $joins[$i][0]['id'],
                'second_view_field_id' => $joins[$i][1]['id'],
            );

            $this->db->insert('budget_view_join_join_view', $budget_view_join_join_view);
        }

        foreach ($fields as $field) {
            $budget_field_join_join_view = array(
                'budget_field_id' => $field['id'],
                'budget_join_view_id' => $budget_join_view_id,
            );

            $this->db->insert('budget_field_join_join_view', $budget_field_join_join_view);
        }

        $firephp->log($budget_join_view_id, 'queryDB out');
    }

}
