<!DOCTYPE html>
<html ng-app="phonecatApp">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet"
              href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
              <?php
              foreach ($css as $style) {
                  echo '<link rel="stylesheet" href="' . $style . '">';
              }
              ?>
        <link rel="stylesheet"
              href="<?php echo base_url()?>assets/css/admin.css">
        <script src="<?php echo base_url(); ?>assets/js/jquery-1.11.1.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <?php
        foreach ($headerJavascripts as $js) {
            echo '<script type="text/javascript" src="' . $js . '"></script>';
        }?>
        <title><?php echo $title; ?></title>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-default main" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="/<?php echo $datainfo['url'] ?>">
                            <?php echo $datainfo['subjekt_nazev'] ?>
                        </a>
                    </div>
                </div>
            </nav>

            <?php
            if (!empty($alerts)) {
                foreach ($alerts as $alert) {
                    echo '<div class="alert alert-' . $alert['class'] . '" role="alert">' . $alert['message'] . '</div>';
                }
            }
            ?>

            <div class='row'>
                <nav class="col-sm-4">
                    <ul class="nav nav-pills nav-stacked">
                        <?php $current = $this->uri->segment(2)?>
                        <li class="<?php if($current == "zakladni") { echo "active"; } ?>"><a href="<?php echo base_url().'admin/zakladni/'.$datainfo['subjekt']; ?>">Základní</a></li>
                        <li class="<?php if($current == "vzhled") { echo "active"; } ?>"><a href="<?php echo base_url().'admin/vzhled/'.$datainfo['subjekt']; ?>">Vzhled</a></li>
                        <li class="<?php if($current == "logo") { echo "active"; } ?>"><a href="<?php echo base_url().'admin/logo/'.$datainfo['subjekt']; ?>">Změna loga</a></li>
                    </ul>
                </nav>
                <div class="col-sm-8">