<?php
foreach($budget as $row) {
    $this->load->view('org/view', $row);
    break;
}

