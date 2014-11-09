angular.module('adminApp', [
    'ngAnimate',
    'ui.router',
    'viewControllers',
    'joinControllers',
    'viewDirectives',
    'checklist-model',
    'angular.filter',
    'viewFilters',
    'ngStorage',
    'viewServices',
    'highcharts-ng',
    'templateControllers',
    'slugifier',
])

// configuring our routes 
// =============================================================================
        .config(function ($stateProvider, $urlRouterProvider, $locationProvider) {

            // when there is an empty route, redirect to /index   
            $stateProvider
		.state('define-view', {
			url: '/define-view/form',
			templateUrl: 'define-view/form.html',
			controller: 'FormController',
		})
		.state('define-view.view-name', {
			url: '/view-name',
			templateUrl: 'define-view/partials/form-view-name.html',
		})
		.state('define-view.table-name', {
			url: '/table-name',
			templateUrl: 'define-view/partials/form-table-name.html',
                        controller: 'TableNameController',
		})
                .state('define-view.table-fields', {
			url: '/table-fields',
			templateUrl: 'define-view/partials/form-table-fields.html',
                        controller: 'TableFieldsController',
		})
                .state('define-view.column-names', {
			url: '/column-names',
			templateUrl: 'define-view/partials/form-column-names.html',
                        controller: 'ColumnsController',
		})
                .state('define-view.group-by', {
			url: '/group-by',
			templateUrl: 'define-view/partials/form-group-by.html',
                        controller: 'GroupByController',
		}).state('define-view.where', {
			url: '/where',
			templateUrl: 'define-view/partials/form-where.html',
                        controller: 'WhereController',
		}).state('define-view.display', {
			url: '/display',
			templateUrl: 'define-view/partials/form-display.html',
                        controller: 'DisplayController',
		}).state('define-view.add-rows', {
			url: '/add-rows',
			templateUrl: 'define-view/partials/form-add-rows.html',
		});
                
        $stateProvider
		.state('join-views', {
			url: '/join-views/form',
			templateUrl: 'join-views/form.html',
                        controller: 'JoinFormController',
		}).state('join-views.view-name', {
			url: '/view-name',
			templateUrl: 'join-views/partials/form-view-name.html',
		}).state('join-views.views', {
			url: '/views',
			templateUrl: 'join-views/partials/form-views.html',
                        controller: 'JoinViewsController',
		}).state('join-views.fields', {
			url: '/fields',
			templateUrl: 'join-views/partials/form-fields.html',
                        controller: 'JoinFieldsController',
		});
                
        $stateProvider
		.state('define-template', {
			url: '/define-template/form',
			templateUrl: 'define-template/form.html',
                        controller: 'TemplateFormController',
		}).state('define-template.template-name', {
			url: '/template-name',
			templateUrl: 'define-template/partials/form-template-name.html',
                        controller: 'TemplateNameController',
		}).state('define-template.template-html-template', {
			url: '/html-template',
			templateUrl: 'define-template/partials/form-template-html-template.html',
                        controller: 'TemplateHtmlTemplateController',
		}).state('define-template.template-fields', {
			url: '/fields',
			templateUrl: 'define-template/partials/form-template-fields.html',
                        controller: 'TemplateFieldsController',
		});
                
        $stateProvider
		.state('success', {
			url: '/success',
			templateUrl: 'success.html',
		});

            // catch all route
            // send users to the form page 
            $urlRouterProvider.otherwise('/define-view/form/view-name');

            $locationProvider.html5Mode(true);
            $locationProvider.hashPrefix('!');
        });

