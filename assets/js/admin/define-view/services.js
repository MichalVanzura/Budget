var viewServices = angular.module('viewServices', []);

viewServices.factory('queryDatabase', ['$http', function ($http) {
        return {
            getTables: function (tables, joins, distinct, fields, where, groupby, limit) {
                return $http({
                    method: 'POST',
                    url: '/database/queryDatabase/',
                    data: $.param
                            ({
                                tables: tables,
                                joins: joins,
                                distinct: distinct,
                                fields: fields,
                                where: where,
                                groupby: groupby,
                                limit: limit
                            }),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                });
            }
        };
    }]);