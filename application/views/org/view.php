<h2 class="text-center"><?php echo $datainfo['rok'] ?></h2>
<br />
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-4 col-lg-push-4">
            <table class="table table-bordered table-hover">
                <tr>
                    <td><a href="/vynosy/organizace/<?php echo $datainfo["subjekt"] ?>/<?php echo $datainfo['rok'] ?>">Výnosy</a></td>
                    <td class="text-right"><?php echo formatAmount($revenuesSum) ?></td>
                </tr>
                <tr>
                    <td><a href="/naklady/organizace/<?php echo $datainfo["subjekt"] ?>/<?php echo $datainfo['rok'] ?>">Náklady</a></td>
                    <td class="text-right"><?php echo formatAmount($costsSum) ?></td>
                </tr>
                <tr>
                    <td>Hosp. výsledky</td>
                    <td class="text-right"><?php echo formatAmount($revenuesSum - $costsSum) ?></td>
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
            credits: {
                enabled: false
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
                },
            },
        });


        $('#incomeChart<?php echo $datainfo['rok'] ?>').highcharts({
            title: {
                text: 'Výnosy'
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
                    name: 'Výnosy',
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
                text: 'Náklady'
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
                    name: 'Náklady',
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

</script>
