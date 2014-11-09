<h2 class="text-center"><?php echo $datainfo['rok'] ?></h2>
<br />
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div id="incomeChart<?php echo $datainfo['rok'] ?>"></div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div id="outcomeChart<?php echo $datainfo['rok'] ?>"></div>
        </div>
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
                        enabled: true,
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                            width: '100px'
                        },
                    },
                }
            },
        });


        $('#incomeChart<?php echo $datainfo['rok'] ?>').highcharts({
            title: {
                text: 'Příjmy'
            },
            plotOptions: {
                pie: {
                    showInLegend: false,
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
            plotOptions: {
                pie: {
                    showInLegend: false,
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
                align: 'right',
                x: -70,
                verticalAlign: 'top',
                y: 20,
                floating: true,
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
                align: 'right',
                x: -70,
                verticalAlign: 'top',
                y: 20,
                floating: true,
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

</script>