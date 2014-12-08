<?php

require 'aws.phar';

use Aws\S3\S3Client;
use Aws\S3\Exception;
use Aws\S3\Enum\CannedAcl;
use Aws\Sqs\SqsClient;

$s3Client = S3Client::factory(array(
	'key' => $_SERVER['AWS_ACCESS_KEY_ID'],
	'secret' => $_SERVER['AWS_SECRET_KEY']
));

$sqsClient = SQSClient::factory(array(
	'key' => $_SERVER['AWS_ACCESS_KEY_ID'],
	'secret' => $_SERVER['AWS_SECRET_KEY'],
	'region' => 'us-east-1'
));

$messageJSON = file_get_contents("php://input");
$message = json_decode($messageJSON,true);

$imageId = $message["imageID"];
$url = $message["image"];
$annotationBlob = $message["annotations"];

try {
	// Create image
	$image = imagecreatefromjpeg($url);

	// Create color
	$yellow = imagecolorallocate($image, 255, 243, 96);

	// Set line thickness
	imagesetthickness($image, 5);

	// Loop through annotations
	foreach($annotationBlob as $annotation){
		// Check for rectangle or line annotation
		if ($annotation[0] == 'rect'){
			// Draw rectangle for rectangle annotations
			imagerectangle($image, floatval($annotation[1]), floatval($annotation[2]), floatval($annotation[3]), floatval($annotation[4]), $yellow);
		} else {
			// Draw line for line annotations
			imageline($image, floatval($annotation[1]), floatval($annotation[2]), floatval($annotation[3]), floatval($annotation[4]), $yellow);
		}
	}

	imagejpeg($image,"/tmp/processedImage_".$imageId.".jpg",90);

	$upload = $s3Client->putObject(array(
		'Bucket' => 'nasa-jpl',
		'Key'    => $imageId.".jpg",
		'Body'   => file_get_contents("/tmp/processedImage_".$imageId.".jpg"),
		'ACL'	 => CannedAcl::PUBLIC_READ,
		'ContentType' => 'image/jpeg'
	));

	imagedestroy($image);

	// Output header
	ob_start();
	header("HTTP/1.1 200 OK");
	ob_end_flush();

	// Just die already
	die();
} catch (Exception\S3Exception $e) {
	error_log($e->getMessage());

	ob_start();
	header("HTTP/1.1 500 Internal Server Error");
	ob_end_flush();

	die();
}

?>