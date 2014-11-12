<?php
//======================================================================================================================
// This file is part of the Mudpuppy PHP framework, released under the MIT License. See LICENSE for full details.
//======================================================================================================================

namespace Model;
use Mudpuppy\DataObject;
use Mudpuppy\App;
use Aws\Sqs\SqsClient;
use Aws\S3\S3Client;
use Aws\S3\Enum\CannedAcl;

defined('MUDPUPPY') or die('Restricted');

/**
 * The data object for Annotation. This class was auto generated, DO NOT remove or edit # comments.
 *
 * #BEGIN MAGIC PROPERTIES
 * @property int id
 * @property int imageId
 * @property string annotations
 * @property string category
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
		$this->createColumn('annotations', DATATYPE_BINARY, NULL, false, 65536);
		$this->createColumn('category', DATATYPE_STRING, NULL, false, 100);

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
	 * @param $category
	 * @return Annotation
	 */
	public static function annotateImage($image, $annotationBlob, $category){
		$annotation = new self();
		$annotation->imageId = $image;
		$annotation->annotation = $annotationBlob;
		$annotation->category = $category;

		$annotation->save();

		App::getDBO()->prepare('SELECT * FROM images WHERE id = '.$image);
		$imageRecord = App::getDBO()->execute()->fetch(\PDO::FETCH_ASSOC);
		$url = $imageRecord['url'];
		$imageId = $imageRecord['imageID']."_".$annotation->id;

		// Setup SQS Client
		$sqsClient = SQSClient::factory(array(
			'key' => $_SERVER['AWS_ACCESS_KEY_ID'],
			'secret' => $_SERVER['AWS_SECRET_KEY'],
			'region' => 'us-west-1'
		));

		// Create the SQS Message
		$sqsMessage = array("imageID"=>$imageId,"image"=>$url,"annotations"=>$annotationBlob);

		// Call SQS to process image
		$sqsClient->sendMessage(array(
			'QueueUrl'    => 'https://sqs.us-west-1.amazonaws.com/026164944188/bah-reinvent-img-proc',
			'MessageBody' => json_encode($sqsMessage)
		));

		/*$s3Client = S3Client::factory(array(
			'key' => 'AKIAJEPFUBJF5RGBL5AQ',
			'secret' => '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',
			'region' => 'us-west-1'
		));

		// Create image
		$image = imagecreatefromjpeg($url);

		// Create color
		$yellow = imagecolorallocate($image, 255, 243, 96);

		// Loop through annotations
		foreach($annotationBlob as $annotation){
			// Check for rectangle or line annotation
			if ($annotation[0] == 'rect'){
				// Draw rectangle for rectangle annotations
				imagerectangle($image, $annotation[1], $annotation[2], $annotation[3], $annotation[4], $yellow);
			} else {
				// Draw line for line annotations
				imageline($image, $annotation[1], $annotation[2], $annotation[3], $annotation[4], $yellow);
			}
		}

		imagejpeg($image,"/tmp/processedImage_".$imageId.".jpg",90);

		$upload = $s3Client->putObject(array(
			'Bucket' => 'bah-reinvent-processed-images',
			'Key'    => $imageId.".jpg",
			'Body'   => file_get_contents("/tmp/processedImage_".$imageId.".jpg"),
			'ACL'	 => CannedAcl::PUBLIC_READ,
			'ContentType' => 'image/jpeg'
		));

		imagedestroy($image);*/

		return $annotation;
	}
}

?>