<?php

class Database_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function getTableNames() {
        $query = $this->db->query("SHOW TABLES LIKE 'data_%'")->result_array();
        return $query;
    }

    public function getTableFields($tableName) {
        $query = $this->db->list_fields($tableName);
        return $query;
    }

    public function queryDatabase($select, $from, $joins, $distinct, $where, $limit, $groupby) {

        //LOG
        $firephp = FirePHP::getInstance(true);

        if ($select != NULL) {
            $this->db->select($select);
        }
        $this->db->from($from);
//        $joinssql = array();
        foreach ($joins as $join) {
            foreach ($join as $key => $value) {
//                $joinstring = sprintf("JOIN `%s` ON %s", $key, $value);
//                array_push($joinssql, $joinstring);
                $this->db->join($key, $value);
            }
        }

//        $distinctsql = '';
        if ($distinct === "true") {
//            $distinctsql = 'DISTINCT';
            $this->db->distinct();
        }

//        $wheresql = array();
//        for ($i = 0; $i < count($where); $i++) {
//            $wherestring = sprintf("`%s`.`%s`%s'%s'", $where[$i]['table_name'], $where[$i]['field'], $where[$i]['operator'], $where[$i]['value']);
//            if ($i == 0) {
//                array_push($wheresql, $wherestring);
//            } else {
//                if ($where[$i]['rel'] === '' || $where[$i]['rel'] === 'and') {
//                    array_push($wheresql, 'AND ' . $wherestring);
//                } else {
//                    array_push($wheresql, 'OR ' . $wherestring);
//                }
//            }
//        }
        foreach ($where as $w) {
            $wherestring = sprintf("%s.%s %s ", $w['table_name'], $w['field'], $w['operator']);            
            if ($w['rel'] === '' || $w['rel'] === 'and') {
                $this->db->where($wherestring, $w['value']);
            } else {
                $this->db->or_where($wherestring, $w['value']);
            }
        }
        if ($groupby != NULL) {
            foreach ($groupby as $gb) {
                $this->db->group_by($gb);
            }
        }

//        $withrusql = '';
//        if ($withru) {
//            $withrusql = 'WITH ROLLUP';
//        }
//        $firephp->log($withru, 'with ru');



//        $rawsql = sprintf(
//                "SELECT %s %s " .
//                "FROM `%s` %s " .
//                "WHERE %s " .
//                "GROUP BY %s %s " .
//                "LIMIT %d", $distinctsql, $select, $from, implode(" ", $joinssql), implode(" ", $wheresql), implode(", ", $groupby), $withrusql, $limit);

//        $query = $this->db->query($rawsql);

        $this->db->limit($limit);
        $query = $this->db->get();

        $out = $this->db->last_query();

        $firephp->log($out, 'queryDB out');

        return $query->result_array();
    }

}
