var joinControllers = angular.module('joinControllers', ['joinServices', 'ngRoute']);

joinControllers.controller('JoinFormController', ['$scope', '$http', '$window',
    function ($scope, $http, $window) {

        $scope.joinViewsData = {};
        $scope.joinViewsData.views = {"0": null};
        $scope.joinViewsData.joins = {};
        $scope.joinViewsData.fields = {};
        $scope.tableData = {};
        $scope.tableData.table = {};

        $http.get('/budgetView/getViews').
                success(function (data) {
                    $scope.views = data;
                });
        $http.get('/budgetView/getViewFields').
                success(function (data) {
                    $scope.viewFields = data;
                });
                
        $scope.createJoinView = function () {
            $joinViews = $scope.joinViewsData;
            $http({
                method: 'POST',
                url: '/budgetView/createJoinView/',
                data: $.param
                        ({
                            name: $joinViews.view_name,
                            views: $joinViews.views,
                            joins: $joinViews.joins,
                            fields: $joinViews.fields
                        }),
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).success(function () {
                $window.location.href = '/angular/success';
            });
        };
    }]);

joinControllers.controller('JoinViewsController', ['$scope', '$http', 'queryJoinViews',
    function ($scope, $http, queryJoinViews) {
        $scope.viewCount = Object.keys($scope.joinViewsData.views).length;
        $scope.click = function () {
            $scope.joinViewsData.joins[$scope.viewCount - 1] = {"0": null, "1": null};
            $scope.joinViewsData.views[$scope.viewCount] = null;
            $scope.viewCount++;
        };
    }]);

joinControllers.controller('JoinFieldsController', ['$scope', '$http', 'queryJoinViews',
    function ($scope, $http, queryJoinViews) {
        $scope.$watchCollection('joinViewsData.fields', function () {
            var handleSuccess = function (data) {
                $scope.tableData.table = data;
            };
            $joinViews = $scope.joinViewsData;
            queryJoinViews.getViews(
                    $joinViews.views,
                    $joinViews.joins,
                    $joinViews.fields)
                    .success(handleSuccess);
        });
    }]);