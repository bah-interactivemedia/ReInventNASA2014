<?php
//======================================================================================================================
// This file is part of the Mudpuppy PHP framework, released under the MIT License. See LICENSE for full details.
//======================================================================================================================

namespace App;

use Mudpuppy\Controller;
use Mudpuppy\DataObjectController;
use Mudpuppy\MudpuppyException;
use Model\Image;

defined('MUDPUPPY') or die('Restricted');

class ImagesController extends Controller {
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
	 * Put Images in DB
	 * @return bool
	 */
	public function action_putImagesInDB(){
		$nasaJSONURL = 'http://json.jpl.nasa.gov/data.json';
		$nasaJSON = file_get_contents($nasaJSONURL);
		$nasaJSONdata = json_decode($nasaJSON, TRUE);

		foreach($nasaJSONdata as $mission => $jsonFile){
			$json = file_get_contents($jsonFile['image_manifest']);
			$data = json_decode($json, TRUE);

			foreach ($data['sols'] as $sol){
				$currentSol = $sol['sol'];
				$solJSONURL = $sol['url'];
				$solJSON = file_get_contents($solJSONURL);
				$solData = json_decode($solJSON, TRUE);

				foreach($solData['pcam_images'] as $solImage){
					foreach($solImage['images'] as $pcamImage){
						if ($pcamImage['color_url'] != null){
							$url = $pcamImage['color_url'];
						} else {
							$url = $pcamImage['url'];
						}

						Image::createImage(
							$pcamImage['imageid'],
							$url,
							$pcamImage['camera_model']['camera_vector'][0],
							$pcamImage['camera_model']['camera_vector'][1],
							$pcamImage['camera_model']['camera_vector'][2],
							$pcamImage['camera_model']['camera_origin'][0],
							$pcamImage['camera_model']['camera_origin'][1],
							$pcamImage['camera_model']['camera_origin'][2],
							$pcamImage['dimensions']['area'][0],
							$pcamImage['dimensions']['area'][1],
							$mission,
							$pcamImage['time']['creation_timestamp_utc']);
					}
				}
			}
		}
	}

	/**
	 * @param int $limit
	 * @param int $offset
	 * @param string $sort
	 * @return array
	 */
	public function action_getImages($limit = 50, $offset = 0, $sort = 'all'){
		return Image::getImages($limit, $offset, $sort);
	}

	/**
	 * @param int $image
	 * @return array
	 */
	public function action_getSameLocationImages($image){
		return Image::getSameLocationImages($image);
	}

	/**
	 * @param Model\Image $image
	 * @return mixed
	 */
	public function action_viewImage($image){
		return $image->viewImage();
	}

	public function action_getAnnotatedImages(){
		return Image::getAnnotatedImages();
	}
}