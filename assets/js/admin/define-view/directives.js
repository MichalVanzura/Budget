var viewDirectives = angular.module('viewDirectives', []);

//Directive for adding buttons on click that show an alert on click
viewDirectives.directive("addbuttons", function($compile){
	return function(scope, element, attrs){
		element.bind("click", function(){
			scope.count++;
			angular.element(document.getElementById('more-tables')).append($compile('<div class="form-group"><select class="form-control" ng-options="tableName for tableName in tableNames" ng-model="formData.table_names['+scope.count+']"  ng-change="change()"><option style="display:none" value="">Vyberte tabulku</option></select></div>')(scope));
		});
	};
});


