angular.module('controllers', [])
	.controller('imagesController', function($scope, $rootScope, $modal, $routeParams, $location, LoadImages, GetImage, ViewImage) {
		$rootScope.loadingText = 'Load More';
		
		// Checks if the route is looking for a specific image
		// Loads a model with the given image information
		if($routeParams.id !== undefined){
			GetImage.query({id: $routeParams.id})
				.$promise.then(function(image){
					var modalInstance = $modal.open({
						templateUrl: 'imageModal.html',
						controller: ImageModal,
						windowClass: 'small ngModalHolder',
						resolve: {
							image: function(){
								var modalImage = image[0];

								// Set post date
								var t = (modalImage.created_on).split(/[- :]/);
								var approveDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]).getTime();
								var today = new Date().getTime();
								var timeDiff = today-approveDate;
								var daysAgo = Math.floor(timeDiff/86400000);

								if (daysAgo == 0){
									modalImage.daysAgo = "today";
								} else if (daysAgo == 1) {
									modalImage.daysAgo = daysAgo + " day ago";
								} else if (daysAgo < 365) {
									modalImage.daysAgo = daysAgo + " days ago";
								} else {
									modalImage.daysAgo = Math.floor(daysAgo/365) + " years ago";
								}

								return modalImage;
							},
							images: function(){
								return [];
							},
							imageIndex: function(){
								return [];
							}
						}
					});
				});
		}

		// Set default image params for querying
		$scope.limit = 50;
		$scope.offset = 0;
		$scope.sortMethod = "all";

		// Create params structure with default params
		$scope.imageParams = {
			"limit": $scope.limit,
			"offset": $scope.offset,
			"sortMethod": $scope.sortMethod
		};

		// Fetch new images
		$scope.images = new LoadImages($scope.imageParams);

		// Sort images
		$scope.imageSort = function(sortMethod){
			$scope.sortMethod = sortMethod;

			// Recreate image params structure
			$scope.imageParams = {
				"limit": $scope.limit,
				"offset": $scope.offset,
				"sortMethod": $scope.sortMethod
			};

			// Get images and immediately invoke nextPage
			$scope.images = new LoadImages($scope.imageParams);
			$scope.images.nextPage();
		};

		// Handle toggling of active class for sort buttons
		$(".sortLinks a:not(.socialLink)").bind('click', function(){
			$(".sortLinks a").removeClass("active");
			$(this).addClass("active");
		});

		// TODO What is this
		$scope.showOverlay = function(image){
			$scope.show = false;
		};

		// Open modal
		$scope.openImageModal = function(image, index){
			var modalInstance = $modal.open({
				templateUrl: 'imageModal.html',
				controller: ImageModal,
				windowClass: 'small ngModalHolder',
				resolve: {
					image: function(){
						return image;
					},
					images: function(){
						return $scope.images.items;
					},
					imageIndex: function(){
						return index;
					}
				}
			});

			$("#imageModal").focus();

			modalInstance.result.then(function (selectedItem) {
		      $scope.selected = selectedItem;
		    }, function () {
		    });
		};

		// Modal functions
		var ImageModal = function($scope, $modalInstance, image, images, imageIndex){
			ViewImage.post({image: image.id});
			$scope.modalImage = image;
			$scope.modalIndex = imageIndex;
			$scope.modalImages = images;

			if (images.length < 2){
				$scope.hideLeft = true;
				$scope.hideRight = true;
			} else {
				checkArrows();
			}

			$scope.showPrevImage = function(modalImage){
				if($scope.modalIndex > 0){
					$scope.modalIndex--; 
					$scope.modalImage = $scope.modalImages[$scope.modalIndex];

					checkArrows();
				}
			};

			$scope.share508 = function(){
				$('.modalShare').click();
			};

			$scope.cancel = function () {
			    $modalInstance.dismiss('cancel');
			  };

			$scope.showNextImage = function(modalImage){
				$scope.modalIndex++; 
				$scope.modalImage = $scope.modalImages[$scope.modalIndex];

				checkArrows();
			};

			//function to check if arrow removal is needed
			function checkArrows(){
				if ($scope.modalIndex == $scope.modalImages.length-1){
					$scope.hideRight = true;
				} else {
					$scope.hideRight = false;
				}

				if ($scope.modalIndex == 0){
					$scope.hideLeft = true;
				} else {
					$scope.hideLeft = false;
				}
			}
		};
	});