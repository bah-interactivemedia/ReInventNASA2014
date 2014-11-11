<?php
require("Mudpuppy/mudpuppy.php");
use Mudpuppy\App;
$controller = App::getPageController();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>NASA JPL</title>

	<!-- JS Frameworks -->
	<script src="/content/js/vendor/jquery.js"></script>
	<script src="/content/js/vendor/modernizr.js"></script>
	<script src="/content/js/vendor/angular.min.js"></script>
	<script src="/content/js/vendor/angular-route.min.js"></script>
	<script src="/content/js/vendor/angular-resource.min.js"></script>
	<script src="/content/js/vendor/imagesLoaded.min.js"></script>
	<script src="/content/js/vendor/ngResponsiveImages.js"></script>
	<script src="/content/js/vendor/masonry.min.js"></script>
	<script src="/content/js/vendor/infiniteScroll.js"></script>

	<script src="/content/js/vendor/mm-foundation.js"></script>

	<!-- CSS Styles -->
	<link rel="stylesheet" href="/content/css/foundation.css">
	<link rel="stylesheet" href="/content/css/style.css">

	<!-- Angular stuff -->
	<script type="text/javascript" src="/content/js/angularApp/imageApp.js"></script>
	<script type="text/javascript" src="/content/js/angularApp/imagesController.js"></script>
	<script type="text/javascript" src="/content/js/angularApp/imagesService.js"></script>
	<script type="text/javascript" src="/content/js/angularApp/imageDirectives.js"></script>

	<?php $controller->renderHeader(); ?>
</head>
<body>
<?php $controller->render(); ?>
	<script src="/content/js/foundation.min.js"></script>
	<script>
		$(document).foundation();
	</script>
</body>
</html>