<?php
sort($budget);
foreach($budget as $year) {
    if ($year['datainfo']['rok'] == date("Y")) {
        $this->load->view('city/view_new', $year);
    }
//    else {
//        $this->load->view('city/view_old', $year);
//    }
//    echo '<br /><br /><br />';
}

