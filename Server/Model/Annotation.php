<?php
//======================================================================================================================
// This file is part of the Mudpuppy PHP framework, released under the MIT License. See LICENSE for full details.
//======================================================================================================================

namespace Model;
use Mudpuppy\DataObject;
use Mudpuppy\App;

defined('MUDPUPPY') or die('Restricted');

/**
 * The data object for Annotation. This class was auto generated, DO NOT remove or edit # comments.
 *
 * #BEGIN MAGIC PROPERTIES
 * @property int id
 * @property int imageId
 * @property string annotation
 * 
 * Foreign Key Lookup Properties
 * @property Image image
 * #END MAGIC PROPERTIES
 */
class Annotation extends DataObject {

	protected function loadDefaults() {
		// Auto-generated code to create columns with default values based on DB schema. DO NOT EDIT.
		// #BEGIN DEFAULTS
		$this->createColumn('id', DATATYPE_INT, NULL, true, 0);
		$this->createColumn('imageId', DATATYPE_INT, NULL, true, 0);
		$this->createColumn('annotation', DATATYPE_BINARY, NULL, false, 65536);

		// Foreign Key Lookups
		$this->createLookup('imageId', 'image', 'Model\Image');
		// #END DEFAULTS

		// Change defaults here if you want user-defined default values
		// $this->updateColumnDefault('column', DEFAULT_VALUE, NOT_NULL);
	}

	public static function getTableName() {
		return 'annotations';
	}

	/**
	 * Fetch a collection of Annotation objects by specified criteria, either by the id, or by any
	 * set of field value pairs (generates query of ... WHERE field0=value0 && field1=value1)
	 * optionally order using field direction pairs [field=>'ASC']
	 * @param int|array $criteria
	 * @param array $order
	 * @param int $limit
	 * @param int $offset
	 * @return Annotation[]
	 */
	public static function fetch($criteria, $order = null, $limit = 0, $offset = 0) {
		return forward_static_call(['Mudpuppy\DataObject', 'fetch'], $criteria, $order, $limit, $offset);
	}

	/**
	 * @param int|array $criteria
	 * @return Annotation|null
	 */
	public static function fetchOne($criteria) {
		return forward_static_call(['Mudpuppy\DataObject', 'fetchOne'], $criteria);
	}

	/** Get all annotations for a specific image
	 * @param $image
	 * @return array
	 */
	public static function getImageAnnotations($image){
		App::getDBO()->prepare('SELECT * FROM annotations WHERE imageId = '.$image);
		$annotations = App::getDBO()->execute()->fetchAll(\PDO::FETCH_CLASS);

		return $annotations;
	}

	/**
	 * @param $image
	 * @param $annotationBlob
	 * @return Annotation
	 */
	public static function annotateImage($image, $annotationBlob, $category){
		$annotation = new self();
		$annotation->imageId = $image;
		$annotation->annotation = $annotationBlob;
		$annotation->category = $category;

		$annotation->save();

		return $annotation;
	}
}

?>