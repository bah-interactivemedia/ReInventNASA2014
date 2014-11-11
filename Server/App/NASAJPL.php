<?php
//======================================================================================================================
// This file is part of the Mudpuppy PHP framework, released under the MIT License. See LICENSE for full details.
//======================================================================================================================

namespace App;

use Mudpuppy\Log;

defined('MUDPUPPY') or die('Restricted');

abstract class NASAJPL {

	private static $security = null;

	public static function initialize() {
		// Perform any application-specific startup tasks here
	}

	public static function getSecurity() {
		if (!self::$security) {
			self::$security = new Security();
		}
		return self::$security;
	}

}

?>