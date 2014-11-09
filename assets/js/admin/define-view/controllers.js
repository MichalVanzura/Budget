var viewControllers = angular.module('viewControllers', ['viewServices', 'ngRoute']);

viewControllers.controller('FormController', ['$scope', '$http', '$window',
    function ($scope, $http, $window) {

        $scope.reset = function () {
            $window.location.href = '/angular/view-name';
        };

//        $scope = $sessionStorage;
        // we will store all of our form data in this object

//        if (sessionStorage.length == 0) {
        $scope.formData = {};
        $scope.tableData = {};
        $scope.tableData.table = {};
        $scope.formData.table_names = {"0": null};
        $scope.tableInfos = {};
        $scope.formData.joins = {};
        $scope.formData.aliases = {};
        $scope.formData.table_fields = [];
        $scope.formData.where = {"0": {"table_name": "", "field": "", "operator": "", "value": "", "rel": ""}};
        $scope.formData.distinct = false;
        $scope.formData.chartCatField = {};
        $scope.formData.chartSerFields = [];
        $scope.formData.chartStacking = '';
//        } else {
//            $scope.formData = $sessionStorage.formData;
//            $scope.tableData = $sessionStorage.tableData;
//            $scope.tableInfos = $sessionStorage.tableInfos;
//        }

        $scope.createView = function () {
            $formdata = $scope.formData;
            $http({
                method: 'POST',
                url: '/budgetView/createView/',
                data: $.param
                        ({
                            viewName: $formdata.view_name,
                            tables: $formdata.table_names,
                            joins: $formdata.joins,
                            distinct: $formdata.distinct,
                            fields: $formdata.table_fields,
                            where: $formdata.where,
                            groupby: $formdata.group_by,
                            chartStacking: $formdata.chartStacking,
                            chartCategory: $formdata.chartCatField,
                            chartFields: $formdata.chartSerFields,
                        }),
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).success(function () {
                $window.location.href = '/angular/success';
            });
        };
    }]);

viewControllers.controller('TableNameController', ['$scope', '$http',
    function ($scope, $http) {
        $scope.tableCount = Object.keys($scope.formData.table_names).length;
        $http.get('/database/getTablesAndFields').
                success(function (data) {
                    $scope.tableNames = Object.keys(data);
                    $scope.tableData.tablesWithFields = data;
                });
        $scope.click = function () {
            $scope.formData.joins[$scope.tableCount - 1] = {"0": null, "1": null};
            $scope.formData.table_names[$scope.tableCount] = null;
            $scope.tableCount++;
        };
    }]);

viewControllers.controller('TableFieldsController', ['$scope', '$http', 'queryDatabase',
    function ($scope, $http, queryDatabase) {
        $http.get('/database/getTableInfos').
                success(function (data) {
                    $scope.tableData.tableInfos = data;
                });

        var handleSuccess = function (data) {
            $scope.tableData.table = data;
        };

        $scope.$watchCollection('formData.table_fields', function () {
            $formdata = $scope.formData;
            queryDatabase.getTables(
                    $formdata.table_names,
                    $formdata.joins,
                    $formdata.distinct,
                    $formdata.table_fields,
                    $formdata.where,
                    $formdata.group_by, 30)
                    .success(handleSuccess);
        });

        $scope.functions = [
            'standard',
            'sum',
        ];
    }]);

viewControllers.controller('ColumnsController', ['$scope',
    function ($scope) {
        $scope.formatters = [
            {name: 'Žádné', value: 'no'},
            {name: 'Číslo', value: 'number'},
            {name: 'Částka', value: 'currency'},
        ];
    }]);

viewControllers.controller('WhereController', ['$scope', 'queryDatabase',
    function ($scope, queryDatabase) {
        $scope.operators = [
            {name: 'Rovná se (=)', value: '='},
            {name: 'Nerovná se (=)', value: '!='},
        ];
        tables = [];
        for (var key in $scope.formData.table_names) {
            tables.push($scope.formData.table_names[key]);
        }
        $scope.tableNames = tables;
        $scope.whereCount = Object.keys($scope.formData.where).length;

        $scope.click = function () {
            $scope.formData.where[$scope.whereCount] = {"table_name": "", "field": "", "operator": "", "value": "", "rel": ""};
            $scope.whereCount++;
        };

        $scope.$watchCollection('formData.where', function () {
            var handleSuccess = function (data) {
                $scope.tableData.table = data;
            };

            $scope.$watchCollection('formData.table_fields', function () {
                $formdata = $scope.formData;
                queryDatabase.getTables(
                        $formdata.table_names,
                        $formdata.joins,
                        $formdata.distinct,
                        $formdata.table_fields,
                        $formdata.where,
                        $formdata.group_by, 30)
                        .success(handleSuccess);
            });
        });
    }]);

viewControllers.controller('GroupByController', ['$scope', 'queryDatabase',
    function ($scope, queryDatabase) {
        $scope.$watchCollection('formData.group_by', function () {
            var handleSuccess = function (data) {
                $scope.tableData.table = data;
            };

            $scope.$watchCollection('formData.table_fields', function () {
                $formdata = $scope.formData;
                queryDatabase.getTables(
                        $formdata.table_names,
                        $formdata.joins,
                        $formdata.distinct,
                        $formdata.table_fields,
                        $formdata.where,
                        $formdata.group_by, 30)
                        .success(handleSuccess);
            });
        });
    }]);

viewControllers.controller('DisplayController', ['$scope',
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

        $scope.catColumnName = "";
        $scope.changeXCategories = function (catColumnName) {
            var rnd = [];
            for (var row in $scope.tableData.table) {
                rnd.push($scope.tableData.table[row][catColumnName.alias]);
            }
            $scope.chartConfig.xAxis.categories = rnd;
        };

        $scope.columnName = $scope.formData.table_fields[0].alias;
        $scope.seriesColor = '';
        $scope.addSeries = function (columnName, seriesType, color) {
            var rnd = [];
            for (var row in $scope.tableData.table) {
                rnd.push(parseInt($scope.tableData.table[row][columnName.alias]));
            }
            $scope.chartConfig.series.push({
                type: seriesType,
                name: columnName.alias,
                data: rnd,
                color: color,
            });
            $scope.formData.chartSerFields.push(columnName);
            
        };

        $scope.colors = [];
        $scope.addPieSeries = function (columnName) {
            var rnd = [];
            for (var row in $scope.tableData.table) {
                rnd.push({
                    name: $scope.chartConfig.xAxis.categories[row],
                    y: parseInt($scope.tableData.table[row][columnName.alias]),
                    color: $scope.colors[row],
                });
                $scope.formData.chartSerFields.push(columnName);
            }
            console.log(rnd);
            $scope.chartConfig.series.push({
                type: 'pie',
                name: columnName.alias,
                data: rnd,
            });
        };

        $scope.removeSeries = function (id) {
            var seriesArray = $scope.chartConfig.series;
            seriesArray.splice(id, 1);
        };
        
        $scope.changeStacking = function() {
            $scope.formData.chartStacking = $scope.chartConfig.options.plotOptions.series.stacking;
        }
    }]);


/**FILTERS**/
var viewFilters = angular.module('viewFilters', []);

viewFilters.filter('split', function () {
    return function (input, splitChar, splitIndex) {
        if (input.match(/\((.*?)\)/) !== null) {
            return input;
        }
        return input.split(splitChar)[splitIndex];
    };
});
