<h2 class="text-center"><?php echo $datainfo['rok'] ?></h2>
<br />
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-4 col-lg-push-4">
            <table class="table table-bordered table-hover">
                <tr>
                    <td><a href="/prijmy/mesto/<?php echo $datainfo["subjekt"] ?>/<?php echo $datainfo['rok'] ?>">Příjmy</a></td>
                    <td class="text-right"><?php echo number_format($revenuesSum, 0, ',', ' ') ?> Kč</td>
                </tr>
                <tr>
                    <td><a href="/vydaje/mesto/<?php echo $datainfo["subjekt"] ?>/<?php echo $datainfo['rok'] ?>">Výdaje</a></td>
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
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div id="incomeBarChart<?php echo $datainfo['rok'] ?>"></div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div id="outcomeBarChart<?php echo $datainfo['rok'] ?>"></div>
        </div>
    </div>
</div>
<script>
    $(function () {
        Highcharts.setOptions({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            lang: {
                numericSymbols: [" tis.", " mil.", "G", "T", "P", "E"],
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
                    point: {
                        events: {
                            legendItemClick: function () {
                                return false; // <== returning false will cancel the default action
                            }
                        }
                    },
                    showInLegend: true,
                }
            },
        });


        $('#incomeChart<?php echo $datainfo['rok'] ?>').highcharts({
            title: {
                text: 'Příjmy'
            },
            legend: {
                align: 'right',
                verticalAlign: 'middle',
                width: 160,
                itemStyle: {
                    width: 150,
                    fontSize: '11px',
                    fontWeight: 'normal',
                }
            },
            series: [{
                    type: 'pie',
                    name: 'Příjmy',
                    data: [
<?php
foreach ($revenuesChart as $key => $value) {
    echo '[\'' . $key . '\',' . $value . '],';
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
            legend: {
                align: 'right',
                verticalAlign: 'middle',
                width: 160,
                itemStyle: {
                    width: 150,
                    fontSize: '11px',
                    fontWeight: 'normal',
                }
            },
            series: [{
                    type: 'pie',
                    name: 'Výdaje',
                    data: [
<?php
foreach ($costsChart as $key => $value) {
    echo '[\'' . $key . '\',' . $value . '],';
}
?>
                    ],
                }]
        });
    });

    $(function () {
        $('#incomeBarChart<?php echo $datainfo['rok'] ?>').highcharts({
            chart: {
                type: 'column',
                marginBottom: 170
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: [
<?php
foreach ($revenuesChart as $key => $value) {
    echo '\'' . $key . '\',';
}
?>
                ]
            },
            yAxis: {
                min: 0,
                title: {
                    text: ''
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            legend: {
                align: 'center',
                verticalAlign: 'top',
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '<br/>' +
                            'Celkem: ' + this.point.stackTotal;
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false,
                    }
                }
            },
            series: [{
                    name: 'Zbývá',
                    data: [
<?php
foreach ($revenuesBudget as $value) {
    echo $value . ',';
}
?>
                    ]
                }, {
                    name: 'Úč <?php echo $datainfo['rok'] ?>',
                    data: [
<?php
foreach ($revenuesChart as $value) {
    echo $value . ',';
}
?>
                    ]
                }]
        });
    });

    $(function () {
        $('#outcomeBarChart<?php echo $datainfo['rok'] ?>').highcharts({
            chart: {
                type: 'column',
                marginBottom: 170
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: [
<?php
foreach ($costsChart as $key => $value) {
    echo '\'' . $key . '\',';
}
?>
                ],
            },
            yAxis: {
                min: 0,
                title: {
                    text: ''
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            legend: {
                align: 'center',
                verticalAlign: 'top',
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '<br/>' +
                            'Celkem: ' + this.point.stackTotal;
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false,
                    }
                }
            },
            series: [{
                    name: 'Zbývá',
                    data: [
<?php
foreach ($costsBudget as $value) {
    echo $value . ',';
}
?>
                    ]
                }, {
                    name: 'Úč <?php echo $datainfo['rok'] ?>',
                    data: [
<?php
foreach ($costsChart as $value) {
    echo $value . ',';
}
?>
                    ]
                }]
        });
    });

    $(function () {
        $('#overviewChart<?php echo $datainfo['rok'] ?>').highcharts({
            title: {
                text: 'Saldo hospodaření'
            },
            xAxis: {
                categories: [<?php echo implode(',', $overviewChart['Kategorie']) ?>]
            },
            yAxis: {
                min: 0,
                title: {
                    text: ''
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
                    data: [<?php echo implode(',', $overviewChart['Příjmy']) ?>]
                }, {
                    type: 'column',
                    name: 'Výdaje',
                    data: [<?php echo implode(',', $overviewChart['Výdaje']) ?>]
                }, {
                    name: 'Saldo',
                    data: [<?php echo implode(',', $overviewChart['Saldo']) ?>],
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                }]
        });
    });

</script>