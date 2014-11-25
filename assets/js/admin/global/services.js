var globalServices = angular.module('globalServices', []);

globalServices.factory('Chart', function () {
    var chartConfig = {
        options: {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
            },
            lang: {
                numericSymbols: [" tis.", " mil.", "G", "T", "P", "E"],
                thousandsSep: " ",
            },
            yAxis: {
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
            plotOptions:
                    {
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
        },
        series: [{
                data: []
            }],
        xAxis: [{
                categories: []
            }],
        title: {
            text: ''
        },
        loading: false
    }
    return chartConfig;
});

