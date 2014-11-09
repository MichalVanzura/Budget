var templateControllers = angular.module('templateControllers', ['ngRoute']);

templateControllers.controller('TemplateFormController', ['$scope', '$http',
    function ($scope, $http) {
        $scope.templateData = {};
        $scope.templateData.templateNameSlug = ''
        $scope.templateData.template_name = '';
        $scope.templateData.html_template = "1";
        $scope.templateData.template_fields = {};
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

templateControllers.controller('TemplateFieldsController', ['$scope', '$http', '$window',
    function ($scope, $http, $window) {
        $http.get('/template/getHtmlTemplateFields/' + $scope.templateData.html_template).
                success(function (data) {
                    $scope.templateData.template_fields = data;
                });
        $http.get('/budgetView/getAllViews').
                success(function (data) {
                    $scope.views = data;
                });
        $scope.displays = [
            {name: 'Tabulka', value: 'table'},
            {name: 'Graf', value: 'chart'},
        ];

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
                            fields: $template.template_fields
                    }),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).success(function () {
                $window.location.href = '/angular/success';
            });
        };
    }]);
