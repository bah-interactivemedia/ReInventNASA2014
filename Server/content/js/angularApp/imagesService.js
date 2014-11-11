angular.module('imagesService', [])
	.factory('GetImage', ['$resource', 'APIURL', function($resource, APIURL){
		return $resource(APIURL+'getImage', {}, {});
	}])
	.factory('ViewImage', ['$resource', 'APIURL', function($resource, APIURL){
		return $resource(APIURL+'viewImage', {}, {
			post: {method: 'POST', params:{}, isArray:false}
		});
	}])
	.factory('LoadImages', ['APIURL', '$http', '$rootScope', function(APIURL, $http, $rootScope) {
		var LoadImages = function(params) {
			this.imageParams = params;
		    this.items = [];
		    this.busy = false;
		    this.after = 0;
		 };

		LoadImages.prototype.nextPage = function() {
		    if (this.busy) return;
		    this.busy = true;
			$rootScope.loadingText = 'Loading...';
			if (this.items == 0){
				this.after = 0;
			} else {
		   		this.after += 50;
			}

		    var url = APIURL+"getImages?limit="+this.imageParams.limit
				+"&offset="+this.after
				+"&sort="+this.imageParams.sortMethod;
		    $http.get(url).success(function(data) {
				if (data.length == 0){
					$rootScope.loadingText = 'No more images to load';
					this.busy = true;
				} else {
					for (var i = 0; i < data.length; i++) {
						/*
						// Set post date
						var t = (data[i].approveDate).split(/[- :]/);
						var approveDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]).getTime();
						var today = new Date().getTime();
						var timeDiff = today-approveDate;
						var daysAgo = Math.floor(timeDiff/86400000);

						if (daysAgo == 0){
							data[i].daysAgo = "today";
						} else if (daysAgo < 365) {
							data[i].daysAgo = daysAgo + " days ago";
						} else {
							data[i].daysAgo = Math.floor(daysAgo/365) + " years ago";
						}*/

						this.items.push(data[i]);
					}

					$rootScope.loadingText = 'Load More';
				}

				this.busy = false;
		    }.bind(this));
		};

	  return LoadImages;
	}]);