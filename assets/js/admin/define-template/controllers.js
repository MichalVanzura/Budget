var templateControllers = angular.module('templateControllers', ['ngRoute']);

templateControllers.controller('TemplateFormController', ['$scope', '$http', '$window',
    function ($scope, $http, $window) {
        $scope.templateData = {};
        $scope.templateData.templateNameSlug = ''
        $scope.templateData.template_name = '';
        $scope.templateData.html_template = "1";
        $scope.templateData.template_fields = {};
        $scope.templateData.parent_view;
        $scope.templateData.parent_key = {};
        $scope.templateData.template_view = {};
        $scope.formData = {};
        $scope.formData.aggregate = {};
        $scope.tableData = {};

        $scope.createTemplate = function () {
            $template = $scope.templateData;
            $http({
                method: 'POST',
                url: '/template/createTemplate/',
                data: $.param
                        ({
                            name: $template.template_name,
                            slug: $template.templateNameSlug,
                            htmlTemplateId: $template.html_template,
                            fields: $template.template_fields,
                            templateView: $template.template_view,
                        }),
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).success(function () {
                $window.location.href = '/angular/success';
            });
        };

        $http.get('/budgetView/getAllViews').
                success(function (data) {
                    $scope.views = data;
                });
    }]);

templateControllers.controller('TemplateNameController', ['$scope', 'Slug',
    function ($scope, Slug) {
        $scope.slugify = function () {
            $scope.templateData.templateNameSlug = Slug.slugify($scope.templateData.template_name);
        };
    }]);

templateControllers.controller('TemplateHtmlTemplateController', ['$scope', '$http',
    function ($scope, $http) {
        $http.get('/template/getHtmlTemplates').
                success(function (data) {
                    $scope.htmlTemplates = data;
                });
    }]);

templateControllers.controller('TemplateFieldsController', ['$scope', '$http',
    function ($scope, $http) {
        $scope.displays = [
            {name: 'Tabulka', value: 'table'},
            {name: 'Graf', value: 'chart'},
        ];

        $http.get('/template/getHtmlTemplateFields/' + $scope.templateData.html_template).
                success(function (data) {
                    $scope.templateData.template_fields = data;
                });
    }]);

templateControllers.controller('TemplateParentViewController', ['$scope', '$http',
    function ($scope, $http) {
        $scope.templateData.template_view.type = 'row';
//        $scope.views = [];
//        for (i in $scope.templateData.template_fields) {
//            $scope.views.push($scope.templateData.template_fields[i].view)
//        }

        $http({
            method: 'POST',
            url: '/budgetView/getViewsTables/',
            data: $.param
                    ({
                        views: $scope.views,
                    }),
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
        }).success(function (data) {
            $scope.viewsTables = data;
        });

        $scope.changeTable = function (tableName) {
            $http.get('/database/getTableFields/' + tableName).
                    success(function (data) {
                        $scope.tableFields = Object.keys(data);
                    });
        };

        $scope.values = [];
        $scope.changeField = function (field) {
            for (i in $scope.tableData.table) {
                $scope.values.push($scope.tableData.table[i][field.alias]);
            }
        };

        $scope.changeView = function (view) {
            if (view.category === 'simple') {
                $http.get('/budgetView/getSimpleViewById/' + view.id).
                        success(function (data) {
                            $scope.formData.view_name = data.view.name;
                            $scope.formData.table_fields = data.fields;
                            $scope.tableData.table = data.tableData;
                        });
            } else {
                $http.get('/budgetView/getJoinViewById/' + view.id).
                        success(function (data) {
                            $scope.formData.view_name = data.view.name;
                            $scope.formData.table_fields = data.fields;
                            $scope.tableData.table = data.tableData;
                            $scope.formData.aggregate.hasAggregate = data.hasAggregate;
                        });
            }
        };
    }]);

/***********/
/* DISPLAY */
/***********/

templateControllers.controller('TemplateShowController', ['$scope', '$http', '$stateParams', '$filter',
    function ($scope, $http, $stateParams, $filter) {
        function setView(scopeForm, scopeTable, scopeChart, field) {
            if (field.field.budget_view_id !== null) {
                $http.get('/budgetView/getSimpleViewById/' + field.field.budget_view_id + '/' + $stateParams.subject + '/' + $stateParams.year + $stateParams.value).
                        success(function (data) {
                            setScopes(scopeForm, scopeTable, scopeChart, data);
                        });
            } else {
                $http.get('/budgetView/getJoinViewById/' + field.field.budget_join_view_id + '/' + $stateParams.subject + '/' + $stateParams.year + $stateParams.value).
                        success(function (data) {
                            setScopes(scopeForm, scopeTable, scopeChart, data);
                            scopeForm.aggregate = data.aggregate;
                        });
            }
        }

        function setScopes(scopeForm, scopeTable, scopeChart, data) {
            scopeForm.view_name = data.view.name;
            scopeForm.table_fields = data.fields;
            scopeTable.table = data.tableData;
            scopeForm.links = data.links;
            scopeForm.chart = data.chart;
            scopeForm.aggregate = data.aggregate;
            console.log(scopeForm.chart);
            if (scopeForm.chart !== null) {
                scopeChart.config = getChartConfig(scopeForm, scopeTable);
            }
        }

        $scope.template = {};
        $http.get('/template/getTemplatesBySlug/' + $stateParams.templateName).
                success(function (template) {
                    $scope.template = template;

                    for (i in template.fields) {
                        var field = template.fields[i];
                        $scope[field.html_field.code_name + 'Form'] = {};
                        $scope[field.html_field.code_name + 'Table'] = {};
                        $scope[field.html_field.code_name + 'Chart'] = {};

                        $scope[field.html_field.code_name + 'Display'] = 'table';
                        if (field['field'].display === 'chart') {
                            $scope[field.html_field.code_name + 'Display'] = 'chart';
                        }

                        setView($scope[field.html_field.code_name + 'Form'], $scope[field.html_field.code_name + 'Table'], $scope[field.html_field.code_name + 'Chart'], field);
                    }
                });


        function getChartConfig(formData, tableData) {
            Highcharts.setOptions({
                title: '',
                lang: {
                    numericSymbols: [" tis.", " mil.", "G", "T", "P", "E"],
                    thousandsSep: " ",
                }
            });

            var chartConfig =
                    {
                        options: {
                            title: '',
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
                        series: [],
                        xAxis:
                                {
                                    categories: {}
                                },
                    };

            //set chart categories
            var field = $filter('filter')(formData.table_fields, function (d) {
                return d.id === formData.chart.category.field_id;
            })[0];
            if(field === undefined) {
                return;
            }
            var categories = [];
            for (i in tableData.table) {
                categories.push(tableData.table[i][field.alias])
            }
            chartConfig.xAxis.categories = categories;

            //set chart series
            var series = formData.chart.series;
            for (i in series) {
                var field = $filter('filter')(formData.table_fields, function (d) {
                    return d.id === series[i].field_id;
                })[0];
                var data = [];
                if (series[i].type !== 'pie') {
                    for (j in tableData.table) {
                        data.push(parseInt(tableData.table[j][field.alias]));
                    }
                }
                else {
                    for (j in tableData.table) {
                        var color = $filter('filter')(field.colors, function (d) {
                            return d.budget_view_field_value === chartConfig.xAxis.categories[j];
                        })[0];
                        data.push(
                                {
                                    name: chartConfig.xAxis.categories[j],
                                    y: parseInt(tableData.table[j][field.alias]),
                                    color: color.color
                                }
                        );
                    }
                }
                chartConfig.series.push
                        (
                                {
                                    type: series[i].type,
                                    color: field.color,
                                    name: field.alias,
                                    data: data
                                }
                        );
                
                if(chartConfig.series[0].type === 'pie') {
                    chartConfig.options.legend = {
                                align: 'right',
                                verticalAlign: 'middle',
                                width: 160,
                                itemStyle: {
                                    width: 150,
                                    fontSize: '11px',
                                    fontWeight: 'normal',
                                },
                            };
                }
            }
            return chartConfig;
        }
    }]);