angular.module('imageDirectives',['ng'])
	.directive('masonry', function(){
		return {
			restrict: 'AC',
			link: function(scope,elem, attrs){
				scope.items = [];
                var container = elem[0];
                var options = angular.extend({
                    itemSelector: '.image'
                }, JSON.parse(attrs.masonry));

                scope.obj = new Masonry(container, options);
			}
		}
	})
	.directive('imageTile', function(){
		return {
			restrict: 'ACE',
			link: function(scope, elem){
				var master = elem.parent('*[masonry]:first').scope();
                var masonry = master.obj;

				setTimeout(function() {
                    masonry.layout();
                }, 0);

                elem.ready(function() {
                    masonry.addItems([elem]);
                    masonry.reloadItems();
                    masonry.layout();
                });

				elem.bind('mouseenter', function(){
					$(elem).find('.imageOverlay').show();
				});
				elem.bind('mouseleave', function(){
					$(elem).find('.imageOverlay').hide();
				});
			}
		}
	})
	.directive('ngEnter', function () {
		return function (scope, element, attrs) {
			element.bind("keydown keypress", function (event) {

				if(event.which === 13) {
					scope.$apply(function (){
						scope.$eval(attrs.ngEnter);
					});

					event.preventDefault();
				}
			});
		};
	})
	.directive('ngFocus', function(){
		return function (scope, element, attrs){
			element.bind("keydown keypress", function (event){
				if(event.which === 9){
					$('#imageModal').focus();
				}
			})
		}
	})
	.directive('addthisToolbox', ['$timeout', function($timeout) {
		return {
			restrict: 'A',
			transclude: true,
			replace: true,
			template: '<div ng-transclude></div>',
			link: function($scope, element, attrs) {
				$timeout(function () {
					addthis.init();
					addthis.button('#addThis'+attrs.imageid, {}, {url: attrs.url});
				});
			}
		}
	}])
	.directive('addthisModalToolbox', ['$timeout', function($timeout) {
		return {
			restrict: 'A',
			transclude: true,
			replace: true,
			template: '<div ng-transclude></div>',
			link: function($scope, element, attrs) {
				$timeout(function () {
					addthis.init();
					addthis.button('.modalShareHolder', {}, {url: attrs.url});
				});
			}
		}
	}]);