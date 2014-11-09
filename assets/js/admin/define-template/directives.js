var templateDirectives = angular.module('templateDirectives', []);

//Directive for adding buttons on click that show an alert on click
templateDirectives.directive("overviewTemplate", function () {
    return {
        templateUrl: '.html'
    };
});


