<?php
//======================================================================================================================
// This file is part of the Mudpuppy PHP framework, released under the MIT License. See LICENSE for full details.
//======================================================================================================================

namespace App\Tests;

use Model\Message;
use Model\UserAccount;
use Model\UserSession;
use Mudpuppy\App;
use Mudpuppy\File;
use Mudpuppy\InvalidInputException;
use Mudpuppy\PageController;
use Mudpuppy\PageNotFoundException;

defined('MUDPUPPY') or die('Restricted');

class Controller extends \Mudpuppy\Controller {
	use PageController;

	public function getRequiredPermissions() {

		if (!\Mudpuppy\Config::$debug) {
			throw new PageNotFoundException();
		}

		return array();
	}

	/**
	 * @return array associative array with two keys, 'js' and 'css', each being an array of script paths for use by the
	 * default implementation of renderHeader()
	 */
	public function getScripts() {
		return [
			'js' => ['files/jquery-1.10.2.min.js', 'files/TestHarness.js', 'files/ApiTest.js'],
			'css' => ['files/TestHarness.css']
		];
	}

	/**
	 * Renders the page body.
	 */
	public function render() {
		if ($this->getOption(0) == 'files') {
			$file = implode('/', $this->pathOptions);
			$file = File::cleanPath($file);
			$file = __DIR__ . '/' . $file;
			ob_clean();
			ob_start();
			File::passThrough($file);
			App::cleanExit();
		}


		include('App/Tests/View.php');
	}

	/**
	 * return a list of regular expressions or strings that the page options must match
	 * example: a url of "this-controller/get/42" can be validated by array('#^get/[0-9]+$#');
	 * @return array
	 */
	public function getAllowablePathPatterns() {
		return array('#^files/.*$#');
	}

	/**
	 * Delete everything associated with a test session
	 * @param string $sessionId
	 * @throws \Mudpuppy\InvalidInputException
	 * @return bool
	 */
	public function action_cleanUp($sessionId) {
		// use this to clean up the database from the tests
		// and call it as your last test
	}

}