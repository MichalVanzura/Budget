var joinControllers = angular.module('joinControllers', ['joinServices', 'ngRoute']);

joinControllers.controller('JoinFormController', ['$scope', '$http', '$window',
    function ($scope, $http, $window) {

        $scope.formData = {};
        $scope.formData.views = {"0": null};
        $scope.formData.joins = {};
        $scope.formData.table_fields = {};
        $scope.formData.aggregate = {hasAggregate: 'false'};
        $scope.tableData = {};
        $scope.tableData.table = {};
        $scope.formData.chart = {};
        $scope.formData.chart.serieFields = [];

        $http.get('/budgetView/getViews').
                success(function (data) {
                    $scope.views = data;
                });
        $http.get('/budgetView/getViewFields').
                success(function (data) {
                    $scope.viewFields = data;
                });

        $scope.createJoinView = function () {
            $joinViews = $scope.formData;
            $http({
                method: 'POST',
                url: '/budgetView/createJoinView/',
                data: $.param
                        ({
                            name: $joinViews.view_name,
                            views: $joinViews.views,
                            joins: $joinViews.joins,
                            fields: $joinViews.table_fields,
                            aggregate: $joinViews.aggregate,
                            chart: $joinViews.chart,
                        }),
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).success(function () {
                $window.location.href = '/angular/success';
            });
        };
    }]);

joinControllers.controller('JoinViewsController', ['$scope', '$http', 'queryJoinViews',
    function ($scope, $http, queryJoinViews) {
        $scope.viewCount = Object.keys($scope.formData.views).length;
        $scope.click = function () {
            $scope.formData.joins[$scope.viewCount - 1] = {"0": null, "1": null};
            $scope.formData.views[$scope.viewCount] = null;
            $scope.viewCount++;
        };
    }]);

joinControllers.controller('JoinFieldsController', ['$scope', '$http', 'queryJoinViews',
    function ($scope, $http, queryJoinViews) {
        $scope.operators = [
            '+',
            '-',
            '*',
            '/',
        ];

        $scope.formatters = [
            {name: 'Žádné', value: 'no'},
            {name: 'Číslo', value: 'number'},
            {name: 'Částka', value: 'currency'},
        ];

        function queryData() {
            var handleSuccess = function (data) {
                $scope.tableData.table = data;
            };

            $joinViews = $scope.formData;
            queryJoinViews.getViews(
                    $joinViews.views,
                    $joinViews.joins,
                    $joinViews.table_fields,
                    $joinViews.aggregate)
                    .success(handleSuccess);
        }

        $scope.$watchCollection('formData.table_fields', function () {
            queryData();
        });
        
        $scope.updateClick = function() {
            queryData();
        }
    }]);

joinControllers.controller('JoinDisplayController', ['$scope',
    function ($scope) {
        $scope.selectedOptions = 'table';

        $scope.chartTypes = [
            {"id": "line", "title": "Čára"},
            {"id": "spline", "title": "Plynulá čára"},
            {"id": "area", "title": "Plocha"},
            {"id": "areaspline", "title": "Plynulá plocha"},
            {"id": "column", "title": "Sloupcový"},
            {"id": "pie", "title": "Koláčový"},
            {"id": "scatter", "title": "Body"}
        ];
        $scope.chartSeries = [];
        $scope.chartStack = [
            {"id": '', "title": "Žádné"},
            {"id": "normal", "title": "Normální"},
            {"id": "percent", "title": "Procentuální"}
        ];
        
        $scope.formData.chart.stacking = "Žádné";

        $scope.chartConfig = {
            options: {
                chart: {
                    type: 'column'
                },
                plotOptions: {
                    series: {
                        stacking: ''
                    }
                }
            },
            series: $scope.chartSeries,
            title: {
                text: ''
            },
            xAxis: {
                categories: [],
            },
            credits: {
                enabled: true
            },
            loading: false,
            size: {}
        };

        $scope.seriesType = "column";
        
        $scope.changeStacking = function (stacking) {
            $scope.formData.chart.stacking = stacking;
        }

        $scope.catColumnName = "";
        $scope.changeXCategories = function (catColumn) {
            var rnd = [];
            for (var row in $scope.tableData.table) {
                rnd.push($scope.tableData.table[row][catColumn.alias]);
            }
            $scope.chartConfig.xAxis.categories = rnd;
        };

        $scope.columnName = $scope.formData.table_fields[0].alias;
        $scope.seriesColor = '';
        $scope.addSeries = function (column, seriesType, color) {
            var rnd = [];
            for (var row in $scope.tableData.table) {
                rnd.push(parseInt($scope.tableData.table[row][column.alias]));
            }
            $scope.chartConfig.series.push({
                type: seriesType,
                name: column.alias,
                data: rnd,
                color: color,
            });
            $scope.formData.chart.serieFields.push(
                    {
                        field: column,
                        color: color,
                        type: seriesType,
                    }
            );

        };

        $scope.colors = [];
        $scope.addPieSeries = function (column) {
            var rnd = [];
            var colors = [];
            for (var row in $scope.tableData.table) {
                rnd.push({
                    name: $scope.chartConfig.xAxis.categories[row],
                    y: parseInt($scope.tableData.table[row][column.alias]),
                    color: $scope.colors[row],
                });
                colors.push(
                        {
                            value: $scope.chartConfig.xAxis.categories[row],
                            color: $scope.colors[row]
                        }
                );
            }
            $scope.formData.chart.serieFields.push(
                    {
                        field: column,
                        colors: colors,
                        type: 'pie',
                    }
            );
    
            $scope.chartConfig.series.push({
                type: 'pie',
                name: column.alias,
                data: rnd,
            });
        };

        $scope.removeSeries = function (id) {
            $scope.chartConfig.series.splice(id, 1);
            $scope.formData.chart.serieFields.splice(id, 1);
        };
    }]);