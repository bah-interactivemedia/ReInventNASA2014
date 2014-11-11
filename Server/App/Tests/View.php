<?php
defined('MUDPUPPY') or die('Restricted');

use Mudpuppy\App;

// get the current page controller to provide data for rendering our view
$controller = App::getPageController();

?>
<div id="log"></div>

<script type="text/javascript">
</script>

<?php
	foreach (\Mudpuppy\File::getFilesAndFoldersRecursive('App/Tests/files/tests/') as $file) {
		print '<script type="text/javascript" src="files/tests/'.$file.'"></script>' . PHP_EOL;
	}
?>

<script type="text/javascript">
	TestHarness.run();
</script>