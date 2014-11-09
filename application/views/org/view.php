<h2 class="text-center"><?php echo $datainfo['rok'] ?></h2>
<br />
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-4 col-lg-push-4">
            <table class="table table-bordered table-hover">
                <tr>
                    <td><a href="/vynosy/organizace/<?php echo $datainfo["subjekt"]?>">Výnosy</a></td>
                    <td class="text-right"><?php echo formatAmount($revenuesSum) ?></td>
                </tr>
                <tr>
                    <td><a href="/naklady/organizace/<?php echo $datainfo["subjekt"]?>">Náklady</a></td>
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
                        enabled: true,
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
        });
        
        
        $('#incomeChart<?php echo $datainfo['rok'] ?>').highcharts({
            title: {
                text: 'Výnosy'
            },
            series: [{
                    type: 'pie',
                    name: 'Výnosy',
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
                text: 'Náklady'
            },
            series: [{
                    type: 'pie',
                    name: 'Náklady',
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
    
</script>
