<?php

class IMException extends Exception {
    protected $error_sql;
    protected $sqlError     = false;
    
    protected $sqlReason;
    
    
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct($message, $code, $previous);
        
        if (is_string($message) && (!is_bool(strpos($message, "error_in_sql")) || !is_bool(strpos($message, "save")))) {
            $this->sqlError     = true;
            
            $ci = &get_instance();
            
            $this->error_sql    = $ci->db->last_query();
            $this->sqlReason    =  mysql_error();
        }
    }
    
    public function isErrorInSql() {
        return $this->sqlError;
    }
    
    public function getErrorSql() {
        return $this->error_sql;
    }
    
    public function getSqlReason() {
        return $this->sqlReason;
    }
}