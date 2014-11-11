// Starts the application
var app = angular.module('imagesApp', ['ngRoute','ngResource', 
                        'mm.foundation', 'ngResponsiveImages', 'infinite-scroll',
                        'controllers', 'imageDirectives', 'imagesService']);

// Sets a default global variable
app.constant('APIURL', '/images/');

// routes configuration to detect image links
// REQUIED templateUrl, so currently points to blank html page
app.config(['$routeProvider', '$httpProvider',
  	function($routeProvider) {
		$routeProvider
            .when('/image/:id', {
                templateUrl: 'content/partials/imageApp.html',
                controller: 'imagesController'
            })
            .otherwise({
				templateUrl: 'content/partials/imageApp.html',
				controller: 'imagesController'
			}); 
	}
]);

app.filter('getSize', function() {
    return function(image) {
        if (image.featured > 0 ){
            size = "_m";
        } else {
            size = "_s";
        }
        return size;
    };
});

app.filter('getSizeClass', function() {
    return function(image) {
        if (image.featured > 0){
            size = "imageLarge";
        } else {
            size = "";
        }
        return size;
    };
});

app.filter('getSaluteOpacity', function() {
	return function(image) {
		if (image.saluted == true){
			return "saluted";
		} else {
			return "";
		}
	};
});

app.filter('getStarImage', function() {
	return function(image) {
		if (image.saluted == true){
			return "content/images/star_saluted.png";
		} else {
			return "content/images/light_star.png";
		}
	};
});

app.filter('getModalStarImage', function() {
	return function(image) {
		if (image.saluted == true){
			return "content/images/star_saluted.png";
		} else {
			return "content/images/star.png";
		}
	};
});

app.filter('getIsSortedByFeatured', function() {
	return function(image) {
		if (image.featured > 0 && $(".featuredButton").hasClass("active")){
			size = "featuredImage";
		} else {
			size = "";
		}
		return size;
	};
});