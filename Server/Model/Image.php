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
 * @property string location_x
 * @property string location_y
 * @property string location_z
 * @property int width
 * @property int height
 * @property string mission
 * @property int created_on
 * @property int views
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
		$this->createColumn('location_x', DATATYPE_DECIMAL, NULL, false, 0);
		$this->createColumn('location_y', DATATYPE_DECIMAL, NULL, false, 0);
		$this->createColumn('location_z', DATATYPE_DECIMAL, NULL, false, 0);
		$this->createColumn('width', DATATYPE_INT, NULL, false, 0);
		$this->createColumn('height', DATATYPE_INT, NULL, false, 0);
		$this->createColumn('mission', DATATYPE_STRING, NULL, false, 10);
		$this->createColumn('created_on', DATATYPE_DATETIME, NULL, false, 0);
		$this->createColumn('views', DATATYPE_INT, 0, false, 0);

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
	 * @param $url
	 * @param $vector_x
	 * @param $vector_y
	 * @param $vector_z
	 * @param $location_x
	 * @param $location_y
	 * @param $location_z
	 * @param $width
	 * @param $height
	 * @param $mission
	 * @param $created_on
	 * @return array
	 */
	static function createImage($id, $url, $vector_x, $vector_y, $vector_z, $location_x, $location_y, $location_z, $width, $height, $mission, $created_on){
		// Insert image record into database
		$image = new self();
		$image->url		= $url;
		$image->imageID = $id;
		$image->vector_x = $vector_x;
		$image->vector_y = $vector_y;
		$image->vector_z = $vector_z;
		$image->location_x = $location_x;
		$image->location_y = $location_y;
		$image->location_z = $location_z;
		$image->width = $width;
		$image->height = $height;
		$image->mission = $mission;
		$image->created_on = $created_on;
		$image->views = 0;

		$image->save();

		return array("Result"=>1);
	}

	/**************** Image Fetching ***************/

	/** Gets $limit number of images
	 * @param int $limit
	 * @param int $offset
	 * @param string $sort
	 * @return array
	 */
	static function getImages($limit, $offset, $sort){
		$idArray = [];

		App::getDBO()->prepare('SELECT MIN(id) as minID, MAX(id) as maxID FROM images');
		$maxAndMin = App::getDBO()->execute()->fetch(\PDO::FETCH_ASSOC);

		$bounds = $limit*4;

		for($i = 0; $i < $bounds; $i++){
			array_push($idArray, rand($maxAndMin['minID'],$maxAndMin['maxID']));
		}

		if ($sort == 'all'){
			App::getDBO()->prepare('SELECT * FROM images WHERE id IN ('.implode(",",$idArray).') AND width = 1024 AND height = 1024 LIMIT '.$limit);
		} else {
			$offsetClause = "";

			if ($offset != null){
				$offsetClause = "OFFSET ".$offset;
			}

			App::getDBO()->prepare('SELECT * FROM images i INNER JOIN annotations a on i.id = a.imageId
				WHERE width = 1024 AND a.category = "'.$sort.'" LIMIT '.$limit.' '.$offsetClause);
		}

		$images = App::getDBO()->execute()->fetchAll(\PDO::FETCH_CLASS);

		return $images;
	}

	/**
	 * @param int $image
	 * @return array $images
	 */
	static function getSameLocationImages($image){
		App::getDBO()->prepare('SELECT location_x as x, location_y as y, location_z as z FROM images WHERE id = '.$image);
		$locationData = App::getDBO()->execute()->fetch(\PDO::FETCH_ASSOC);

		App::getDBO()->prepare('SELECT * FROM images WHERE location_x = '.$locationData['x'].
			' AND location_y = '.$locationData['y'].
			' AND location_z = '.$locationData['z']);

		$images = App::getDBO()->execute()->fetchAll(\PDO::FETCH_CLASS);

		return $images;
	}

	public function viewImage(){
		$this->views++;
		$this->save();

		return $this;
	}
}

?>