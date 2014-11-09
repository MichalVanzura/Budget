<h2 class="text-center"><?php echo $datainfo['rok'] ?></h2>
<br />
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-4 col-lg-push-4">
            <table class="table table-bordered table-hover">
                <tr>
                    <td><a href="/prijmy/mesto/<?php echo $datainfo["subjekt"]?>">Příjmy</a></td>
                    <td class="text-right"><?php echo number_format($revenuesSum, 0, ',', ' ') ?> Kč</td>
                </tr>
                <tr>
                    <td><a href="/vydaje/mesto/<?php echo $datainfo["subjekt"]?>">Výdaje</a></td>
                    <td class="text-right"><?php echo number_format($costsSum, 0, ',', ' ') ?> Kč</td>
                </tr>
                <tr>
                    <td>Saldo</td>
                    <td class="text-right"><?php echo number_format(($revenuesSum - $costsSum), 0, ',', ' ') ?> Kč</td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-lg-4 col-lg-pull-4">
            <div id="incomeChart<?php echo $datainfo['rok'] ?>"></div>
        </div>
        <div class="col-xs-12 col-sm-6 col-lg-4">
            <div id="outcomeChart<?php echo $datainfo['rok'] ?>"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2 col-lg-6 col-lg-offset-3" id="overviewChart<?php echo $datainfo['rok'] ?>"></div>
    </div>
</div>
<script>    
    $(function () { 
        Highcharts.setOptions({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
//            credits: {
//                enabled: false
//            },
            lang: {
                numericSymbols: [ " tis." , " mil." , "G" , "T" , "P" , "E"],
                thousandsSep: " ",
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    minSize: 1,
                    size: 150,
                    dataLabels: {
                        enabled: false,
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                            width: 'auto'
                        },
                    },
                    showInLegend: true,
                }
            },
        });
        
        
        $('#incomeChart<?php echo $datainfo['rok'] ?>').highcharts({
            title: {
                text: 'Příjmy'
            },
            legend:{
                align: 'right',
                verticalAlign:'middle',
                width: 220,
                itemStyle: {
                    width:210,
                    fontSize:'11px',
                    fontWeight: 'normal',
                }
            },
            series: [{
                    type: 'pie',
                    name: 'Příjmy',
                    data: [
                        <?php
                            foreach($revenuesChart as $key => $value)
                            {
                                echo '[\''.$key.'\','.$value.'],';
                            }
                        ?>
                    ],
                }]
        });
    });
    
    $(function () {
        $('#outcomeChart<?php echo $datainfo['rok'] ?>').highcharts({
            title: {
                text: 'Výdaje'
            },
            legend:{
                align: 'right',
                verticalAlign:'middle',
                width: 220,
                itemStyle: {
                    width:210,
                    fontSize:'11px',
                    fontWeight: 'normal',
                }
            },
            series: [{
                    type: 'pie',
                    name: 'Výdaje',
                    data: [
                        <?php
                            foreach($costsChart as $key => $value)
                            {
                                echo '[\''.$key.'\','.$value.'],';
                            }
                        ?>
                    ],
                }]
        });
    });
    
    $(function () {
    $('#overviewChart<?php echo $datainfo['rok'] ?>').highcharts({
        title: {
            text: 'Saldo hospodaření'
        },
        xAxis: {
            categories: [<?php echo implode(',' , $overviewChart['Kategorie'] )?>]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Miliony'
            }
        },
        credits: {
            enabled: false
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0 0 0 5px;text-align:right"><b>{point.y} Kč</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },        
        series: [{
            type: 'column',
            name: 'Příjmy',
            data: [<?php echo implode(',' , $overviewChart['Příjmy'] )?>]
        }, {
            type: 'column',
            name: 'Výdaje',
            data: [<?php echo implode(',' , $overviewChart['Výdaje'] )?>]
        }, {
            name: 'Saldo',
            data: [<?php echo implode(',' , $overviewChart['Saldo'] )?>],
            marker: {
                lineWidth: 2,
                lineColor: Highcharts.getOptions().colors[3],
                fillColor: 'white'
            }
        }]
    });
    });
    
</script>
