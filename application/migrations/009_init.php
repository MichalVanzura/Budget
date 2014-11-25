<?php

class Migration_Init extends CI_Migration {

    public function up() {
        $overview = $this->createOverview();
        $income = $this->createIncome($overview);
        $outcome = $this->createOutcome($overview);
        $saldo = $this->createSaldo();
        $this->createTownTemplate($overview['overview_id'], $income, $outcome, $saldo);
    }

    public function down() {
        
    }
    
    private function createTownTemplate($overview, $income, $outcome, $saldo) {
        //TEMPLATE
        $this->db->insert('template', array('name' => 'Město', 'slug' => 'mesto', 'html_template_id' => 1));
        $template_id = $this->db->insert_id();
        
        $this->db->insert('template_field', array(
            'template_id' => $template_id, 
            'budget_join_view_id' => $income,
            'display' => 'chart',
            'html_template_field_id' => 1));
        
        $this->db->insert('template_field', array(
            'template_id' => $template_id, 
            'budget_join_view_id' => $outcome,
            'display' => 'chart',
            'html_template_field_id' => 2));
        
        $this->db->insert('template_field', array(
            'template_id' => $template_id, 
            'budget_view_id' => $overview,
            'display' => 'table',
            'html_template_field_id' => 3));
        
        $this->db->insert('template_field', array(
            'template_id' => $template_id, 
            'budget_join_view_id' => $saldo,
            'display' => 'chart',
            'html_template_field_id' => 4));
        
        $this->db->insert('template_view', array(
            'template_id' => $template_id));
    }
    
    private function createOverview() {
        $this->db->insert('budget_view', array('name' => 'Přehled pol', 'distinct_rows' => 0, 'from_table' => 'data_ucetnictvi_pol'));
        $ov_id = $this->db->insert_id();
        
        $this->db->insert('budget_view_field', array(
            'name' => 'nazev',
            'table_name' => 'data_sekce_pol',
            'alias' => 'Sekce',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $ov_id));
        $sekce_id = $this->db->insert_id();
        
        $this->db->insert('budget_view_field', array(
            'name' => 'castka',
            'table_name' => 'data_ucetnictvi_pol',
            'alias' => 'Účetnictví',
            'function' => 'sum',
            'formatter' => 'currency',
            'budget_view_id' => $ov_id));
        
        $this->db->insert('budget_view_filter', array(
            'name' => 'subject',
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'subjekt',
            'budget_view_id' => $ov_id));

        $this->db->insert('budget_view_filter', array(
            'name' => 'year',
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'rok',
            'budget_view_id' => $ov_id));
        
        $this->db->insert('budget_view_group_by', array(
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'sekce_pol',
            'budget_view_id' => $ov_id));
        
        $this->db->insert('budget_view_join', array(
            'first_table' => 'data_ucetnictvi_pol',
            'second_table' => 'data_sekce_pol',
            'first_field' => 'sekce_pol',
            'second_field' => 'kod',
            'budget_view_id' => $ov_id));
        
        return array('overview_id' => $ov_id, 'sekce_id' => $sekce_id);
    }
    
    private function createSaldo() {
        
        //INCOME
        $this->db->insert('budget_view', array('name' => 'Saldo příjmy', 'distinct_rows' => 0, 'from_table' => 'data_ucetnictvi_pol'));
        $inc_id = $this->db->insert_id();
        
        $this->db->insert('budget_view_field', array(
            'name' => 'rok',
            'table_name' => 'data_ucetnictvi_pol',
            'alias' => 'Rok',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $inc_id));
        $field_inc_rok_id = $this->db->insert_id();
        
        $this->db->insert('budget_view_field', array(
            'name' => 'castka',
            'table_name' => 'data_ucetnictvi_pol',
            'alias' => 'Příjmy',
            'function' => 'sum',
            'formatter' => 'currency',
            'budget_view_id' => $inc_id,
            'color' => '#A9FF96'));
        $field_inc_cas_id = $this->db->insert_id();
        
        $this->db->insert('budget_view_filter', array(
            'name' => 'subject',
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'subjekt',
            'budget_view_id' => $inc_id));
        
        $this->db->insert('budget_view_group_by', array(
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'rok',
            'budget_view_id' => $inc_id));
        
        $this->db->insert('budget_view_where', array(
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'sekce_pol',
            'operator' => '=',
            'value' => 'P',
            'rel' => '',
            'budget_view_id' => $inc_id));
        
        //OUTCOME
        $this->db->insert('budget_view', array('name' => 'Saldo výdaje', 'distinct_rows' => 0, 'from_table' => 'data_ucetnictvi_pol'));
        $out_id = $this->db->insert_id();
        
        $this->db->insert('budget_view_field', array(
            'name' => 'rok',
            'table_name' => 'data_ucetnictvi_pol',
            'alias' => 'Rok',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $out_id));
        $field_out_rok_id = $this->db->insert_id();
        
        $this->db->insert('budget_view_field', array(
            'name' => 'castka',
            'table_name' => 'data_ucetnictvi_pol',
            'alias' => 'Výdaje',
            'function' => 'sum',
            'formatter' => 'currency',
            'budget_view_id' => $out_id,
            'color' => '#FF7599'));
        $field_out_cas_id = $this->db->insert_id();
        
        $this->db->insert('budget_view_filter', array(
            'name' => 'subject',
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'subjekt',
            'budget_view_id' => $out_id));
        
        $this->db->insert('budget_view_group_by', array(
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'rok',
            'budget_view_id' => $out_id));
        
        $this->db->insert('budget_view_where', array(
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'sekce_pol',
            'operator' => '=',
            'value' => 'V',
            'rel' => '',
            'budget_view_id' => $out_id));
        
        //JOIN
        $this->db->insert('budget_join_view', array('name' => 'Saldo hospodaření'));
        $join_id = $this->db->insert_id();
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_inc_rok_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_inc_cas_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_out_cas_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_view_join_join_view', array(
            'first_view_id' => $inc_id,
            'second_view_id' => $out_id,
            'first_view_field_id' => $field_inc_rok_id,
            'second_view_field_id' => $field_out_rok_id,
            'budget_join_view_id' => $join_id));
        
        
        //CHART
        $this->db->insert('chart', array('join_view_id' => $join_id));
        $chart_id = $this->db->insert_id();
        
        $this->db->insert('chart_category', array(
            'field_id' => $field_inc_rok_id, 
            'chart_id' => $chart_id));
        
        $this->db->insert('chart_serie', array(
            'field_id' => $field_inc_cas_id, 
            'chart_id' => $chart_id,
            'type' => 'column'));
        
        $this->db->insert('chart_serie', array(
            'field_id' => $field_out_cas_id, 
            'chart_id' => $chart_id,
            'type' => 'column'));
        
        return $join_id;
    }

    private function createIncome($overview) {
        $this->db->insert('budget_view', array('name' => 'Příjmy pol účetnictví', 'distinct_rows' => 0, 'from_table' => 'data_ucetnictvi_pol'));
        $uc_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'polozka_trida',
            'table_name' => 'data_ucetnictvi_pol',
            'alias' => 'Položka třída',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $uc_id));
        $field_uc_kod_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'castka',
            'table_name' => 'data_ucetnictvi_pol',
            'alias' => 'Účetnictví',
            'function' => 'sum',
            'formatter' => 'currency',
            'budget_view_id' => $uc_id));
        $field_uc_id = $this->db->insert_id();

        $this->db->insert('budget_view_filter', array(
            'name' => 'subject',
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'subjekt',
            'budget_view_id' => $uc_id));

        $this->db->insert('budget_view_filter', array(
            'name' => 'year',
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'rok',
            'budget_view_id' => $uc_id));

        $this->db->insert('budget_view_group_by', array(
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'polozka_trida',
            'budget_view_id' => $uc_id));

        $this->db->insert('budget_view_where', array(
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'sekce_pol',
            'operator' => '=',
            'value' => 'P',
            'rel' => '',
            'budget_view_id' => $uc_id));
        
        
        $this->db->insert('budget_view', array('name' => 'Příjmy pol rozpočet', 'distinct_rows' => 0, 'from_table' => 'data_rozpocet_pol'));
        $roz_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'polozka_trida',
            'table_name' => 'data_rozpocet_pol',
            'alias' => 'Položka třída',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $roz_id));
        $field_roz_kod_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'castka',
            'table_name' => 'data_rozpocet_pol',
            'alias' => 'Rozpočet',
            'function' => 'sum',
            'formatter' => 'currency',
            'budget_view_id' => $roz_id));
        $field_roz_id = $this->db->insert_id();

        $this->db->insert('budget_view_filter', array(
            'name' => 'subject',
            'table_name' => 'data_rozpocet_pol',
            'field' => 'subjekt',
            'budget_view_id' => $roz_id));

        $this->db->insert('budget_view_filter', array(
            'name' => 'year',
            'table_name' => 'data_rozpocet_pol',
            'field' => 'rok',
            'budget_view_id' => $roz_id));

        $this->db->insert('budget_view_group_by', array(
            'table_name' => 'data_rozpocet_pol',
            'field' => 'polozka_trida',
            'budget_view_id' => $roz_id));

        $this->db->insert('budget_view_where', array(
            'table_name' => 'data_rozpocet_pol',
            'field' => 'sekce_pol',
            'operator' => '=',
            'value' => 'P',
            'rel' => '',
            'budget_view_id' => $roz_id));
        
        $this->db->insert('budget_view', array('name' => 'Položka třída', 'distinct_rows' => 0, 'from_table' => 'data_polozka_trida'));
        $pol_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'kod',
            'table_name' => 'data_polozka_trida',
            'alias' => 'Kód',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $pol_id));
        $field_kod_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'nazev',
            'table_name' => 'data_polozka_trida',
            'alias' => 'Třída',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $pol_id));
        $field_trida_id = $this->db->insert_id();

        $this->db->insert('budget_view_group_by', array(
            'table_name' => 'data_polozka_trida',
            'field' => 'kod',
            'budget_view_id' => $pol_id));
        
        $this->db->insert('budget_view_where', array(
            'table_name' => 'data_polozka_trida',
            'field' => 'kod',
            'operator' => '<=',
            'value' => '4',
            'rel' => '',
            'budget_view_id' => $pol_id));
        
        
        //JOIN
        $this->db->insert('budget_join_view', array('name' => 'Příjmy'));
        $join_id = $this->db->insert_id();
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_kod_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_trida_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_uc_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_roz_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_view_join_join_view', array(
            'first_view_id' => $pol_id,
            'second_view_id' => $uc_id,
            'first_view_field_id' => $field_kod_id,
            'second_view_field_id' => $field_uc_kod_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_view_join_join_view', array(
            'first_view_id' => $uc_id,
            'second_view_id' => $roz_id,
            'first_view_field_id' => $field_uc_kod_id,
            'second_view_field_id' => $field_roz_kod_id,
            'budget_join_view_id' => $join_id));
        
        
        //COLOR
        $this->db->insert('budget_view_field_color', array(
            'color' => '#8085E9',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'DAŇOVÉ PŘÍJMY'));
        
        $this->db->insert('budget_view_field_color', array(
            'color' => '#434348',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'NEDAŇOVÉ PŘÍJMY'));
        
        $this->db->insert('budget_view_field_color', array(
            'color' => '#95CEFF',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'KAPITÁLOVÉ PŘÍJMY'));
        
        $this->db->insert('budget_view_field_color', array(
            'color' => '#A9FF96',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'PŘIJATÉ TRANSFERY'));
        
        //CHART
        $this->db->insert('chart', array('join_view_id' => $join_id));
        $chart_id = $this->db->insert_id();
        
        $this->db->insert('chart_category', array(
            'field_id' => $field_trida_id, 
            'chart_id' => $chart_id));
        
        $this->db->insert('chart_serie', array(
            'field_id' => $field_uc_id, 
            'chart_id' => $chart_id,
            'type' => 'pie'));
        
        //TEMPLATE
        $this->db->insert('template', array('name' => 'Příjmy', 'slug' => 'prijmy', 'html_template_id' => 2));
        $template_id = $this->db->insert_id();
        
        $this->db->insert('template_field', array(
            'template_id' => $template_id, 
            'budget_join_view_id' => $join_id,
            'display' => 'chart',
            'html_template_field_id' => 5));
        
        $this->db->insert('template_field', array(
            'template_id' => $template_id, 
            'budget_join_view_id' => $join_id,
            'display' => 'table',
            'html_template_field_id' => 6));
        
        $this->db->insert('template_view', array(
            'template_id' => $template_id,
            'view_id' => $overview['overview_id'],
            'view_field_id' => $overview['sekce_id'],
            'view_field_value' => 'Příjmy'));
        
        return $join_id;
    }
    
    private function createOutcome($overview) {
        $this->db->insert('budget_view', array('name' => 'Výdaje pol účetnictví', 'distinct_rows' => 0, 'from_table' => 'data_ucetnictvi_pol'));
        $uc_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'paragraf_skupina',
            'table_name' => 'data_ucetnictvi_pol',
            'alias' => 'Paragraf skupina',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $uc_id));
        $field_uc_kod_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'castka',
            'table_name' => 'data_ucetnictvi_pol',
            'alias' => 'Účetnictví',
            'function' => 'sum',
            'formatter' => 'currency',
            'budget_view_id' => $uc_id));
        $field_uc_id = $this->db->insert_id();

        $this->db->insert('budget_view_filter', array(
            'name' => 'subject',
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'subjekt',
            'budget_view_id' => $uc_id));

        $this->db->insert('budget_view_filter', array(
            'name' => 'year',
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'rok',
            'budget_view_id' => $uc_id));

        $this->db->insert('budget_view_group_by', array(
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'paragraf_skupina',
            'budget_view_id' => $uc_id));

        $this->db->insert('budget_view_where', array(
            'table_name' => 'data_ucetnictvi_pol',
            'field' => 'sekce_pol',
            'operator' => '=',
            'value' => 'V',
            'rel' => '',
            'budget_view_id' => $uc_id));
        
        
        $this->db->insert('budget_view', array('name' => 'Výdaje pol rozpočet', 'distinct_rows' => 0, 'from_table' => 'data_rozpocet_pol'));
        $roz_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'paragraf_skupina',
            'table_name' => 'data_rozpocet_pol',
            'alias' => 'Paragraf skupina',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $roz_id));
        $field_roz_kod_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'castka',
            'table_name' => 'data_rozpocet_pol',
            'alias' => 'Rozpočet',
            'function' => 'sum',
            'formatter' => 'currency',
            'budget_view_id' => $roz_id));
        $field_roz_id = $this->db->insert_id();

        $this->db->insert('budget_view_filter', array(
            'name' => 'subject',
            'table_name' => 'data_rozpocet_pol',
            'field' => 'subjekt',
            'budget_view_id' => $roz_id));

        $this->db->insert('budget_view_filter', array(
            'name' => 'year',
            'table_name' => 'data_rozpocet_pol',
            'field' => 'rok',
            'budget_view_id' => $roz_id));

        $this->db->insert('budget_view_group_by', array(
            'table_name' => 'data_rozpocet_pol',
            'field' => 'paragraf_skupina',
            'budget_view_id' => $roz_id));

        $this->db->insert('budget_view_where', array(
            'table_name' => 'data_rozpocet_pol',
            'field' => 'sekce_pol',
            'operator' => '=',
            'value' => 'V',
            'rel' => '',
            'budget_view_id' => $roz_id));
        
        $this->db->insert('budget_view', array('name' => 'Paragraf skupina', 'distinct_rows' => 0, 'from_table' => 'data_paragraf_skupina'));
        $pol_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'kod',
            'table_name' => 'data_paragraf_skupina',
            'alias' => 'Kód',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $pol_id));
        $field_kod_id = $this->db->insert_id();

        $this->db->insert('budget_view_field', array(
            'name' => 'nazev',
            'table_name' => 'data_paragraf_skupina',
            'alias' => 'Skupina',
            'function' => 'standard',
            'formatter' => 'no',
            'budget_view_id' => $pol_id));
        $field_trida_id = $this->db->insert_id();

        $this->db->insert('budget_view_group_by', array(
            'table_name' => 'data_paragraf_skupina',
            'field' => 'kod',
            'budget_view_id' => $pol_id));
        
        
        //JOIN
        $this->db->insert('budget_join_view', array('name' => 'Výdaje'));
        $join_id = $this->db->insert_id();
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_kod_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_trida_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_uc_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_field_join_join_view', array(
            'budget_field_id' => $field_roz_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_view_join_join_view', array(
            'first_view_id' => $pol_id,
            'second_view_id' => $uc_id,
            'first_view_field_id' => $field_kod_id,
            'second_view_field_id' => $field_uc_kod_id,
            'budget_join_view_id' => $join_id));
        
        $this->db->insert('budget_view_join_join_view', array(
            'first_view_id' => $uc_id,
            'second_view_id' => $roz_id,
            'first_view_field_id' => $field_uc_kod_id,
            'second_view_field_id' => $field_roz_kod_id,
            'budget_join_view_id' => $join_id));
        
        
        //COLOR
        $this->db->insert('budget_view_field_color', array(
            'color' => '#8085E9',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'ZEMĚDĚLSTVÍ A LESNÍ HOSPODÁŘSTVÍ'));
        
        $this->db->insert('budget_view_field_color', array(
            'color' => '#434348',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'PRŮMYSLOVÁ A OSTATNÍ ODVĚTVÍ HOSPODÁŘSTVÍ'));
        
        $this->db->insert('budget_view_field_color', array(
            'color' => '#95CEFF',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'SLUŽBY PRO OBYVATELSTVO'));
        
        $this->db->insert('budget_view_field_color', array(
            'color' => '#A9FF96',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'SOCIÁLNÍ VĚCI A POLITIKA ZAMĚSTNANOSTI'));
        
        $this->db->insert('budget_view_field_color', array(
            'color' => '#8d4653',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'BEZPEČNOST STÁTU A PRÁVNÍ OCHRANA'));
        
        $this->db->insert('budget_view_field_color', array(
            'color' => '#91e8e1',
            'budget_view_field_id' => $field_uc_id,
            'budget_view_field_value' => 'VŠEOBECNÁ VEŘEJNÁ SPRÁVA A SLUŽBY'));
        
        //CHART
        $this->db->insert('chart', array('join_view_id' => $join_id));
        $chart_id = $this->db->insert_id();
        
        $this->db->insert('chart_category', array(
            'field_id' => $field_trida_id, 
            'chart_id' => $chart_id));
        
        $this->db->insert('chart_serie', array(
            'field_id' => $field_uc_id, 
            'chart_id' => $chart_id,
            'type' => 'pie'));
        
        //TEMPLATE
        $this->db->insert('template', array('name' => 'Výdaje', 'slug' => 'vydaje', 'html_template_id' => 2));
        $template_id = $this->db->insert_id();
        
        $this->db->insert('template_field', array(
            'template_id' => $template_id, 
            'budget_join_view_id' => $join_id,
            'display' => 'chart',
            'html_template_field_id' => 5));
        
        $this->db->insert('template_field', array(
            'template_id' => $template_id, 
            'budget_join_view_id' => $join_id,
            'display' => 'table',
            'html_template_field_id' => 6));
        
        $this->db->insert('template_view', array(
            'template_id' => $template_id,
            'view_id' => $overview['overview_id'],
            'view_field_id' => $overview['sekce_id'],
            'view_field_value' => 'Výdaje'));
        
        return $join_id;
    }

}
