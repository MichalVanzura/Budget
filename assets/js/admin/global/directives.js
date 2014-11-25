var globalDirectives = angular.module('globalDirectives', []);

globalDirectives.directive('viewTable', function () {
    return{
        restrict: 'AEC',
        scope:
                {
                    formData: "=form",
                    tableData: "=table",
                },
        templateUrl: 'global/viewTable.html',
    }
});

globalDirectives.directive('viewChart', function () {
    return{
        restrict: 'AEC',
        scope:
                {
                    chartConfig: "=chart",
                },
        templateUrl: 'global/viewChart.html',
    }
});


