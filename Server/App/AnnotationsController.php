<?php
//======================================================================================================================
// This file is part of the Mudpuppy PHP framework, released under the MIT License. See LICENSE for full details.
//======================================================================================================================

namespace App;

use Mudpuppy\Controller;
use Mudpuppy\DataObjectController;
use Mudpuppy\MudpuppyException;
use Model\Annotation;
use Model\Image;

defined('MUDPUPPY') or die('Restricted');

class AnnotationsController extends Controller {
	use DataObjectController;

	public function getRequiredPermissions() {
		// TODO add specific required permissions if any to access this controller
		return array();
	}

	/**
	 * Determines whether an input object is valid prior to creating or updating it. Note: the input array has already
	 * been cleaned and validated against the structure definition.
	 * @param $object array representation of the object
	 * @return boolean true if valid
	 */
	protected function isValid($object) {
		// TODO validate the object
		return false;
	}

	/**
	 * Sanitizes the array representation of the object prior to returning to the user.
	 * @param array $object array representation of the object
	 * @return array that represents the sanitized object
	 */
	protected function sanitize($object) {
		// TODO sanitize the object if necessary
		return $object;
	}

	/**
	 * Uncomment to override the fetching of a collection '/DataObject/?p=1&...' using $params from the request
	 *
	 * Retrieves an array of DataObjects for use by getCollection. The default implementation returns ALL objects.
	 * Override to support filtering based on the query parameters.
	 * @param array $params array of query parameters that came in with the request
	 * @return array(DataObject)
	 */
//	protected function retrieveDataObjects($params) {
//		return call_user_func(array($this->getDataObjectName(), 'getAll'));
//	}

	/**
	 * Get annotations by image
	 * @param int $image
	 * @return array
	 */
	public function action_getImageAnnotations($image){
		return Annotation::getImageAnnotations($image);
	}

	/** Annotates an image
	 * @param int $image
	 * @param string $annotationBlob
	 * @return Annotation
	 */
	public function action_annotateImage($image, $annotationBlob){
		return Annotation::annotateImage($image, $annotationBlob);
	}
}