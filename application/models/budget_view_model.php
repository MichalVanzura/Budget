<?php

class Budget_view_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getViews() {
        return $this->db->get('budget_view')->result_array();
    }

    public function getViewFields() {
        return $this->db->get('budget_view_field')->result_array();
    }

    public function getSimpleViewTablesById($id) {

        $view = $this->db->get_where('budget_view', array('id' => $id))->row_array();

        $result = array();
        array_push($result, $view['from_table']);
        $joins = $this->db->get_where('budget_view_join', array('budget_view_id' => $id))->result_array();
        foreach ($joins as $join) {
            array_push($result, $join['second_table']);
        }

        return $result;
    }

    public function getJoinViewTablesById($id) {
        $viewIds = $this->db->get_where('budget_view_join_join_view', array('budget_join_view_id' => $id))->result_array();
        $result = array();

        foreach ($viewIds as $id) {

            $tables = $this->getSimpleViewTablesById($id['first_view_id']);
            foreach ($tables as $table) {
                array_push($result, $table);
            }
        }
        return $result;
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

    public function getViewSQL($id, $subject = NULL, $year = NULL, $value = NULL) {
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

        $filters = $this->db->get_where('budget_view_filter', array('budget_view_id' => $id))->result_array();
        if ($filters != NULL) {
            $and = 'AND';
            if (count($whereArray) == 0) {
                $and = '';
            }
            foreach ($filters as $filter) {
                if ($filter['name'] == 'subject' && $subject != NULL) {
                    $wherestr = sprintf("%s `%s`.`%s`='%s'", $and, $filter['table_name'], $filter['field'], $subject);
                    array_push($whereArray, $wherestr);
                    $and = 'AND';
                }
                if ($filter['name'] == 'year' && $year != NULL) {
                    $wherestr = sprintf("%s `%s`.`%s`='%s'", $and, $filter['table_name'], $filter['field'], $year);
                    array_push($whereArray, $wherestr);
                    $and = 'AND';
                }
                if ($filter['name'] == 'value' && $value != NULL) {
                    $wherestr = sprintf("%s `%s`.`%s`='%s'", $and, $filter['table_name'], $filter['field'], $value);
                    array_push($whereArray, $wherestr);
                    $and = 'AND';
                }
            }
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

        $firephp->log($rawsql);
        return $rawsql;
    }

    public function createView($viewName, $distinct, $from, $joins, $fields, $where, $groupby, $chart, $filters) {
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


        if ($chart != NULL) {
            $chartDB = array(
                'view_id' => $budget_view_id,
                'stacking' => $chart["stacking"],
            );
            $this->db->insert('chart', $chartDB);
            $chart_id = $this->db->insert_id();

            $chart['catField']['budget_view_id'] = $budget_view_id;
            $fieldDB = $this->db->get_where('budget_view_field', $chart['catField'])->row_array();
            $category = array(
                'field_id' => $fieldDB['id'],
                'chart_id' => $chart_id,
            );
            $this->db->insert('chart_category', $category);

            foreach ($chart['serieFields'] as $chartField) {
                $chartField['field']['budget_view_id'] = $budget_view_id;
                $fieldDB = $this->db->get_where('budget_view_field', $chartField['field'])->row_array();

                $serie = array(
                    'field_id' => $fieldDB['id'],
                    'chart_id' => $chart_id,
                    'type' => $chartField['type'],
                );

                $this->db->where('id', $fieldDB['id']);
                $this->db->update('budget_view_field', array('color' => $chartField['color']));

                if ($chartField['colors'] != NULL) {
                    foreach ($chartField['colors'] as $field_color) {
                        $budget_view_field_color = array(
                            'color' => $field_color['color'],
                            'budget_view_field_id' => $fieldDB['id'],
                            'budget_view_field_value' => $field_color['value']
                        );

                        $this->db->insert('budget_view_field_color', $budget_view_field_color);
                    }
                }
                $this->db->insert('chart_serie', $serie);
            }

            if ($filters != null) {
                foreach ($filters as $key => $val) {
                    if ($val != '') {
                        $budget_view_filter = array(
                            'name' => $key,
                            'table_name' => $val['table_name'],
                            'field' => $val['field_name'],
                            'budget_view_id' => $budget_view_id,
                        );

                        $this->db->insert('budget_view_filter', $budget_view_filter);
                    }
                }
            }
        }


        $firephp->log($budget_view_id, 'queryDB out');
    }

    public function createJoinView($viewName, $views, $joins, $fields, $aggregate, $chart) {
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

        if ($aggregate['hasAggregate'] == 'true') {
            $insert_aggregate = array(
                'budget_join_view_id' => $budget_join_view_id,
                'first_view_id' => $aggregate['first_view']['id'],
                'second_view_id' => $aggregate['second_view']['id'],
                'first_view_field_id' => $aggregate['first_field']['id'],
                'second_view_field_id' => $aggregate['second_field']['id'],
                'alias' => $aggregate['alias'],
                'formatter' => $aggregate['formatter'],
                'operator' => $aggregate['operator'],
                'color' => $aggregate['color'],
            );

            $this->db->insert('aggregate', $insert_aggregate);
        }

        foreach ($fields as $field) {
            $budget_field_join_join_view = array(
                'budget_field_id' => $field['id'],
                'budget_join_view_id' => $budget_join_view_id,
            );

            $this->db->insert('budget_field_join_join_view', $budget_field_join_join_view);
        }


        if ($chart != NULL) {
            $chartDB = array(
                'join_view_id' => $budget_join_view_id,
                'stacking' => $chart["stacking"],
            );
            $this->db->insert('chart', $chartDB);
            $chart_id = $this->db->insert_id();


            $category = array(
                'field_id' => $chart['catField']['id'],
                'chart_id' => $chart_id,
            );
            $this->db->insert('chart_category', $category);

            foreach ($chart['serieFields'] as $chartField) {
                $serie = array(
                    'field_id' => $chartField['field']['id'],
                    'chart_id' => $chart_id,
                    'type' => $chartField['type'],
                );

                $this->db->where('id', $chartField['field']['id']);
                $this->db->update('budget_view_field', array('color' => $chartField['color']));

                if ($chartField['colors'] != NULL) {
                    foreach ($chartField['colors'] as $field_color) {
                        $budget_view_field_color = array(
                            'color' => $field_color['color'],
                            'budget_view_field_id' => $chartField['field']['id'],
                            'budget_view_field_value' => $field_color['value']
                        );

                        $this->db->insert('budget_view_field_color', $budget_view_field_color);
                    }
                }
                $this->db->insert('chart_serie', $serie);
            }
        }

        $firephp->log($budget_join_view_id, 'queryDB out');
    }

    public function getSimpleViewById($id, $subject, $year, $value) {
        //LOG
        $firephp = FirePHP::getInstance(true);

        $result['view'] = $this->db->get_where('budget_view', array('id' => $id))->row_array();
        $result['tableData'] = $this->db->query($this->getViewSQL($id, $subject, $year, $value))->result_array();
        $result['fields'] = $this->db->get_where('budget_view_field', array('budget_view_id' => $id))->result_array();

        $result['links'] = array();
        $j = 0;
        foreach ($result['fields'] as $field) {
            $links = $this->db->get_where('template_view', array('view_field_id' => $field['id']))->result_array();
            if (!empty($links)) {
                for ($i = 0; $i < count($links); $i++) {
                    $template = $this->db->get_where('template', array('id' => $links[$i]['template_id']))->row_array();

                    $result['links'][$links[$i]['view_field_value']] = $links[$i];
                    $result['links'][$links[$i]['view_field_value']]['url'] = $template['slug'] . '/' . $subject . '/' . $year;
                    $result['links'][$links[$i]['view_field_value']]['alias'] = $field['alias'];
                }
            }

            $colors = $this->db->get_where('budget_view_field_color', array('budget_view_field_id' => $field['id']))->result_array();
            $result['fields'][$j]['colors'] = $colors;
            $j++;
        }
        $result['chart'] = $this->getSimpleViewChartByViewId($id);
        return $result;
    }

    public function getSimpleViewChartByViewId($viewId) {
        $chart = $this->db->get_where('chart', array('view_id' => $viewId))->row_array();
        $result = NULL;
        if (!empty($chart)) {
            $result = array(
                'config' => $chart,
                'category' => $this->db->get_where('chart_category', array('chart_id' => $chart['id']))->row_array(),
                'series' => $this->db->get_where('chart_serie', array('chart_id' => $chart['id']))->result_array()
            );
        }
        return $result;
    }

    public function getJoinViewById($id, $subject, $year, $value) {
        $firephp = FirePHP::getInstance(true);

        $this->db->from('budget_join_view');
        $this->db->where('id', $id);
        $result['view'] = $this->db->get()->row_array();
        $result['tableData'] = $this->db->query($this->getJoinViewSQL($id, $subject, $year, $value))->result_array();
        $result['fields'] = array();
        $fieldJoins = $this->db->get_where('budget_field_join_join_view', array('budget_join_view_id' => $id))->result_array();
        foreach ($fieldJoins as $fieldJoin) {
            $field = $this->db->get_where('budget_view_field', array('id' => $fieldJoin['budget_field_id']))->row_array();
            array_push($result['fields'], $field);
        }
        $result['aggregate'] = $this->db->get_where('aggregate', array('budget_join_view_id' => $id))->row_array();
        if (!empty($result['aggregate'])) {
            $result['aggregate']['hasAggregate'] = TRUE;
        }

        $result['links'] = array();
        $j = 0;
        foreach ($result['fields'] as $field) {
            $links = $this->db->get_where('template_view', array('view_field_id' => $field['id']))->result_array();
            if (!empty($links)) {
                for ($i = 0; $i < count($links); $i++) {
                    $template = $this->db->get_where('template', array('id' => $links[$i]['template_id']))->row_array();

                    $result['links'][$links[$i]['view_field_value']] = $links[$i];
                    $result['links'][$links[$i]['view_field_value']]['url'] = $template['slug'] . '/' . $subject . '/' . $year;
                    $result['links'][$links[$i]['view_field_value']]['alias'] = $field['alias'];
                }
            }

            $colors = $this->db->get_where('budget_view_field_color', array('budget_view_field_id' => $field['id']))->result_array();
            $result['fields'][$j]['colors'] = $colors;
            $j++;
        }

        $result['chart'] = $this->getJoinViewChartByViewId($id);
        $firephp->log($result, 'result join view');
        return $result;
    }

    public function getJoinViewChartByViewId($viewId) {
        $chart = $this->db->get_where('chart', array('join_view_id' => $viewId))->row_array();
        $result = array(
            'config' => $chart,
            'category' => $this->db->get_where('chart_category', array('chart_id' => $chart['id']))->row_array(),
            'series' => $this->db->get_where('chart_serie', array('chart_id' => $chart['id']))->result_array()
        );
        return $result;
    }

    public function getJoinViewSQL($id, $subject, $year, $value) {
        //LOG
        $firephp = FirePHP::getInstance(true);

        $fieldJoins = $this->db->get_where('budget_field_join_join_view', array('budget_join_view_id' => $id))->result_array();
        $selectArray = array();
        foreach ($fieldJoins as $fieldJoin) {
            $field = $this->db->get_where('budget_view_field', array('id' => $fieldJoin['budget_field_id']))->row_array();
            $selectstr = sprintf("`view%s`.`%s`", $field['budget_view_id'], $field['alias']);
            array_push($selectArray, $selectstr);
        }

        $aggregate = $this->db->get_where('aggregate', array('budget_join_view_id' => $id))->row_array();
        if (!empty($aggregate)) {
            $firstField = $this->db->get_where('budget_view_field', array('id' => $aggregate['first_view_field_id']))->row_array();
            $secondField = $this->db->get_where('budget_view_field', array('id' => $aggregate['second_view_field_id']))->row_array();
            $aggregatestr = sprintf("(`view%d`.`%s` %s `view%d`.`%s`) AS '%s'", $aggregate['first_view_id'], $firstField['alias'], $aggregate['operator'], $aggregate['second_view_id'], $secondField['alias'], $aggregate['alias']);
            array_push($selectArray, $aggregatestr);
        }

        $joinsArray = array();
        $joins = $this->db->get_where('budget_view_join_join_view', array('budget_join_view_id' => $id))->result_array();
        for ($i = 0; $i < count($joins); $i++) {
            if ($i == 0) {
                $fromSQL = $this->getViewSQL($joins[$i]['first_view_id'], $subject, $year, $value);
                $from = sprintf("(%s) AS `view%d`", $fromSQL, $joins[$i]['first_view_id']);
            }
            $viewSQL = $this->getViewSQL($joins[$i]['second_view_id'], $subject, $year, $value);
            $firstField = $this->db->get_where('budget_view_field', array('id' => $joins[$i]['first_view_field_id']))->row_array();
            $secondField = $this->db->get_where('budget_view_field', array('id' => $joins[$i]['second_view_field_id']))->row_array();
            $joinstr = sprintf("LEFT JOIN (%s) AS `view%d` ON `view%d`.`%s`=`view%d`.`%s`", $viewSQL, $joins[$i]['second_view_id'], $joins[$i]['first_view_id'], $firstField['alias'], $joins[$i]['second_view_id'], $secondField['alias']);
            array_push($joinsArray, $joinstr);
        }
        $sql = sprintf("SELECT %s FROM %s %s", implode(', ', $selectArray), $from, implode(' ', $joinsArray));
        $firephp->log($sql, 'join view sql');
        return $sql;
        //$result = $this->db->query($sql)->result_array();
    }

    public function queryJoinView($views, $joins, $fields, $aggregate) {
        //LOG
        $firephp = FirePHP::getInstance(true);

        $i = 0;
        $from = '';
        $previousId = 0;
        $joinsArray = array();
        foreach ($views as $view) {
            $viewSQL = $this->getViewSQL($view['id']);
            if ($i == 0) {
                $from = sprintf("(%s) AS `view%d`", $viewSQL, $view['id']);
            } else {
                $joinstr = sprintf("LEFT JOIN (%s) AS `view%d` ON `view%d`.`%s`=`view%d`.`%s`", $viewSQL, $view['id'], $previousId, $joins[$i - 1][0]['alias'], $view['id'], $joins[$i - 1][1]['alias']);
                array_push($joinsArray, $joinstr);
            }
            $previousId = $view['id'];
            $i++;
        }

        $selectArray = array();
        foreach ($fields as $field) {
            $selectstr = sprintf("`view%s`.`%s`", $field['budget_view_id'], $field['alias']);
            array_push($selectArray, $selectstr);
        }

        if ($aggregate['hasAggregate'] == 'true') {
            $aggregatestr = sprintf("(`view%d`.`%s` %s `view%d`.`%s`) AS '%s'", $aggregate['first_view']['id'], $aggregate['first_field']['alias'], $aggregate['operator'], $aggregate['second_view']['id'], $aggregate['second_field']['alias'], $aggregate['alias']);
            array_push($selectArray, $aggregatestr);
        }


        $sql = sprintf("SELECT %s FROM %s %s", implode(', ', $selectArray), $from, implode(' ', $joinsArray));
        $query = $this->db->query($sql)->result_array();
        return $query;
    }

}
