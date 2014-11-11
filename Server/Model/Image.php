<?php
//======================================================================================================================
// This file is part of the Mudpuppy PHP framework, released under the MIT License. See LICENSE for full details.
//======================================================================================================================

namespace Model;
use Mudpuppy\DataObject;
use Aws\S3\S3Client;
use Aws\Sqs\SqsClient;
use Aws\Ses\SesClient;
use Mudpuppy\Config;
use Mudpuppy\App;
use Mudpuppy\Log;
use Mudpuppy\MudpuppyException;

defined('MUDPUPPY') or die('Restricted');

/**
 * The data object for Image. This class was auto generated, DO NOT remove or edit # comments.
 *
 * #BEGIN MAGIC PROPERTIES
 * @property int id
 * @property string url
 * @property string imageID
 * @property string vector_x
 * @property string vector_y
 * @property string vector_z
 * @property int created_on
 * 
 * Foreign Key Lookup Properties
 * #END MAGIC PROPERTIES
 */
class Image extends DataObject {

	protected function loadDefaults() {
		// Auto-generated code to create columns with default values based on DB schema. DO NOT EDIT.
		// #BEGIN DEFAULTS
		$this->createColumn('id', DATATYPE_INT, NULL, true, 0);
		$this->createColumn('url', DATATYPE_STRING, NULL, true, 1000);
		$this->createColumn('imageID', DATATYPE_STRING, NULL, true, 50);
		$this->createColumn('vector_x', DATATYPE_DECIMAL, NULL, false, 0);
		$this->createColumn('vector_y', DATATYPE_DECIMAL, NULL, false, 0);
		$this->createColumn('vector_z', DATATYPE_DECIMAL, NULL, false, 0);
		$this->createColumn('created_on', DATATYPE_DATETIME, NULL, false, 0);

		// Foreign Key Lookups
		// #END DEFAULTS

		// Change defaults here if you want user-defined default values
		// $this->updateColumnDefault('column', DEFAULT_VALUE, NOT_NULL);
	}

	public static function getTableName() {
		return 'images';
	}

	/**
	 * Fetch a collection of Image objects by specified criteria, either by the id, or by any
	 * set of field value pairs (generates query of ... WHERE field0=value0 && field1=value1)
	 * optionally order using field direction pairs [field=>'ASC']
	 * @param int|array $criteria
	 * @param array $order
	 * @param int $limit
	 * @param int $offset
	 * @return Image[]
	 */
	public static function fetch($criteria, $order = null, $limit = 0, $offset = 0) {
		return forward_static_call(['Mudpuppy\DataObject', 'fetch'], $criteria, $order, $limit, $offset);
	}

	/**
	 * @param int|array $criteria
	 * @return Image|null
	 */
	public static function fetchOne($criteria) {
		return forward_static_call(['Mudpuppy\DataObject', 'fetchOne'], $criteria);
	}


	/**************** Image Creation ***************/

	/**
	 * @param $id
	 * @param string $url
	 * @param $vector_x
	 * @param $vector_y
	 * @param $vector_z
	 * @param $created_on
	 * @return array
	 */
	static function createImage($id, $url, $vector_x, $vector_y, $vector_z, $created_on){
		// Insert image record into database
		$image = new self();
		$image->url		= $url;
		$image->imageID = $id;
		$image->vector_x = $vector_x;
		$image->vector_y = $vector_y;
		$image->vector_z = $vector_z;
		$image->created_on = $created_on;

		$image->save();

		return array("Result"=>1);
	}

	/**************** Image Fetching ***************/

	/**
	 * Gets $limit number of images
	 * @param  int $limit  number of images
	 * @return array $images 	Images
	 */
	static function getImages($limit){
		$idArray = [];

		App::getDBO()->prepare('SELECT MAX(id) as maxID FROM images');
		$maxID = App::getDBO()->execute()->fetch(\PDO::FETCH_ASSOC);

		for($i = 0; $i < $limit; $i++){
			array_push($idArray, rand(0,$maxID['maxID']));
		}

		App::getDBO()->prepare('SELECT * FROM images WHERE id IN ('.implode(",",$idArray).')');
		$images = App::getDBO()->execute()->fetchAll(\PDO::FETCH_CLASS);

		return $images;
	}
}

?>