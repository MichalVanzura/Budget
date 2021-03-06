var joinServices = angular.module('joinServices', []);

joinServices.factory('queryJoinViews', ['$http', function ($http) {
        return {
            getViews: function (views, joins, fields, aggregate) {
                return $http({
                    method: 'POST',
                    url: '/budgetView/queryJoinViews/',
                    data: $.param
                            ({
                                views: views,
                                joins: joins,
                                fields: angular.copy(fields),
                                aggregate: aggregate,
                            }),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                });
            }
        };
    }]);