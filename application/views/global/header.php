<!DOCTYPE html>
<html>
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
              href="<?php echo base_url()?>assets/css/default.css">
        <script src="<?php echo base_url(); ?>assets/js/jquery-1.11.1.min.js"
        type="text/javascript"></script>
        <script type="text/javascript"
        src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <?php
        foreach ($headerJavascripts as $js) {
            echo '<script type="text/javascript" src="' . $js . '"></script>';
        }

        if(!empty($appearance)) { ?>
        <style>
            .navbar.main {
                background-color: <?php if($appearance['header_color'] != NULL){ echo $appearance['header_color']; } ?>;
            }
        </style>
        <?php 
        }?>
        <title><?php echo $title; ?></title>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-default main" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse"
                                data-target="#main-menu">
                            <span class="sr-only">Toggle navigation</span> <span
                                class="icon-bar"></span> <span class="icon-bar"></span> <span
                                class="icon-bar"></span>
                        </button>
                        <span class="navbar-brand">
                            <img class="logo" src="<?php if(!empty($appearance) && $appearance['logo_path'] != NULL){ echo base_url().$appearance['logo_path']; } ?>" alt="">
                            Rozpočet
                        </span>
                    </div>

                    <div class="collapse navbar-collapse" id="main-menu">
                        <ul class="nav navbar-nav navbar-right"> 
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo($datainfo['subjekt_nazev']); ?><span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="<?php echo base_url().'admin/zakladni/'.$datainfo['subjekt']; ?>">Administrace</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <?php if(!empty($subMenu)) {?>
            <div class="container-fluid">
                <div class="row">
                    <nav class="navbar navbar-default col-md-5" role="navigation">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse"
                                        data-target="#years-menu">
                                    <span class="sr-only">Toggle navigation</span> <span
                                        class="icon-bar"></span> <span class="icon-bar"></span> <span
                                        class="icon-bar"></span>
                                </button>
                                <span class="navbar-brand" >Rok</span>
                            </div>

                            <div class="collapse navbar-collapse" id="years-menu">
                                <ul class="nav navbar-nav">
                                    <?php $yearsMenu = $this->dynamic_menu->build_years_menu($datainfo['subjekt']); ?>
                                    <?php
                                    $pre = '';
                                    if(isset($prefix)) {
                                        $pre = $prefix;
                                    } 
                                    foreach ($yearsMenu as $item) {
                                        echo '<li><a href="' . base_url(). $pre . $item['url'] . '/' . $item['rok'] . '">' . $item['rok'] . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </nav>
                    <nav class="navbar navbar-default col-md-5 col-md-offset-2" role="navigation">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse"
                                        data-target="#secondary-menu">
                                    <span class="sr-only">Toggle navigation</span> <span
                                        class="icon-bar"></span> <span class="icon-bar"></span> <span
                                        class="icon-bar"></span>
                                </button>
                                <span class="navbar-brand" >Subjekt</span>
                            </div>

                            <div class="collapse navbar-collapse" id="secondary-menu">
                                <ul class="nav navbar-nav navbar-right">
                                    <?php $subjectMenu = $this->dynamic_menu->build_subject_menu($datainfo['subjekt']); ?>
                                    <li><a href="<?php echo base_url() . $subjectMenu['parent']['url'] ?>"> <?php echo $subjectMenu['parent']['title'] ?></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Městské organizace <span class="caret"></span></a>
                                        <ul class="dropdown-menu" role="menu">
                                        <?php
                                        foreach ($subjectMenu['children'] as $child) {
                                            echo '<li><a href="' . base_url() . $child['url'] . '">' . $child['title'] . '</a></li>';
                                        }
                                        ?>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            <?php }?>

            <?php
            if (!empty($alerts)) {
                foreach ($alerts as $alert) {
                    echo '<div class="alert alert-' . $alert['class'] . '" role="alert">' . $alert['message'] . '</div>';
                }
            }
            ?>