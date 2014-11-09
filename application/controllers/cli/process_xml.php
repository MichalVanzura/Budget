<?php

class Process_xml extends CI_Controller {
    
    private $xpath;
    private $insertedRowCount;
    
    public function __construct() {
        parent::__construct();
        $this->load->library('migration');
        $this->migration->latest();
        $this->load->library('input');
        $this->load->dbforge();
        $this->load->title('Zpracovat XML');
    }

    public function index() {
        // if (! $this->input->is_cli_request ()) {
        // echo "This script can only be accessed via the command line" . PHP_EOL;
        // return;
        // }
        // else {
        // }
        if (glob("uploads/queue/*.*")) {

            $file_path = 'uploads/queue/';
            $file_name = '';

            $h = opendir('uploads/queue');
            while (false !== ($entry = readdir($h))) {
                if ($entry != '.' && $entry != '..') {
                    $file_path = $file_path . $entry;
                    $file_name = $entry;
                    break;
                }
            }

            $this->db->trans_start();
            $this->parseXMLFile($file_path);
            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                unlink($file_path);
            }

            $this->load->addAlert('Soubor ' . $file_name . ' byl úspěšně parsován. Vloženo ' . $this->insertedRowCount . ' záznamů', ALERT_SUCCESS);
            $this->load->template('default');
        } else {
            $this->load->addAlert('Ve frontě nejsou žádné soubory k parsování.', ALERT_WARNING);
            $this->load->template('default');
        }
    }

    public function xsdTypeToSqlType($type) {
        switch ($type) {
            case "xs:string" :
                return "TEXT";
            case "xs:short" :
                return "INT";
            case "xs:int" :
                return "INT";
            case "xs:boolean" :
                return "BOOL";
            case "xs:decimal" :
                return "DECIMAL";
        }
    }

    public function createTables() {
        $elementDefs = $this->xpath->evaluate("//DataSet/xs:schema/xs:element/xs:complexType/xs:choice/xs:element");
        foreach ($elementDefs as $elementDef) {
            $tableName = 'data_'.strtolower($elementDef->getAttribute('name'));
            $fields = array();

            $elementDefs = $this->xpath->evaluate("xs:complexType/xs:sequence/xs:element", $elementDef);
            foreach ($elementDefs as $elementDef) {
                $type = $this->xsdTypeToSqlType($elementDef->getAttribute('type'));
                $fields [strtolower($elementDef->getAttribute('name'))] = array(
                    'type' => $type,
                );
            }
            $this->dbforge->add_field($fields);
            $this->dbforge->add_field('id');
            $this->dbforge->create_table($tableName, TRUE);
        }
    }

    public function insertData() {
        $elementDefs = $this->xpath->evaluate("/DataSet/*[position()>1]");
        $this->insertedRowCount = 0;
        foreach ($elementDefs as $elementDef) {
            $data = array();
            $tableName = 'data_'.strtolower($elementDef->tagName);

            $children = $elementDef->childNodes;
            foreach ($children as $child) {
                $data [strtolower($child->nodeName)] = $child->nodeValue;
                $this->db->where($child->nodeName, $child->nodeValue);
            }
            if ($this->db->table_exists($tableName)) {

                $this->db->from($tableName);
                $countResults = $this->db->count_all_results();
                $fieldExist = $this->db->field_exists('subjekt', $tableName);

                if ($countResults == 0 || $fieldExist && $tableName != 'datainfo') {
                    // A record does not exist, insert one.
                    $this->db->insert($tableName, $data);
                    $this->insertedRowCount++;
                }
            }
        }
    }
    
    public function createMenuEntry() {
        $dataInfoId = $this->xpath->evaluate("/DataSet/DataInfo/Subjekt")->item(0)->nodeValue;
        $dataInfoName = $this->xpath->evaluate("/DataSet/DataInfo/Subjekt_Nazev")->item(0)->nodeValue;
        $dataInfoParentId = $this->xpath->evaluate("/DataSet/DataInfo/NadrazenySubjekt")->item(0)->nodeValue;
        $dataInfoParentName = $this->xpath->evaluate("/DataSet/DataInfo/NadrazenySubjekt_Nazev")->item(0)->nodeValue;
        
        $parentData = array(
            'title' => $dataInfoParentName,
            'url' => $dataInfoParentId,
            'subject' => $dataInfoParentId,
        );
        $this->db->where('title', $dataInfoParentName)->from('menu');
        if($this->db->count_all_results() == 0) {
            $this->db->insert('menu', $parentData); 
        }
        
        $url = '';
        if($dataInfoParentId == $dataInfoId) {
            $url = 'mesto/'.$dataInfoId;
        } else {
            $url = 'organizace/'.$dataInfoId;
        }
        
        $data = array(
            'title' => $dataInfoName,
            'url' => $url,
            'parent_subject' => $dataInfoParentId,
            'subject' => $dataInfoId,
        );
        $this->db->where('title', $dataInfoName)->from('menu');
        if($this->db->count_all_results() == 0) {
            $this->db->insert('menu', $data); 
        }
        else {
            $this->db->where('title', $dataInfoName);
            $this->db->update('menu', $data); 
        }
    }

    public function parseXMLFile($file) {
        $doc = new DOMDocument ();
        $xsdstring = simplexml_load_file($file)->asXML();
        $doc->preserveWhiteSpace = FALSE;
        $doc->loadXML(mb_convert_encoding($xsdstring, 'utf-8', mb_detect_encoding($xsdstring)));
        $this->xpath = new DOMXPath($doc);
        $this->xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');

        $this->createMenuEntry();
        $this->createTables();
        $this->insertData();
    }

}
