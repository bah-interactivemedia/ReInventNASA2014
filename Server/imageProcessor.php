<?php

require_once('aws.phar');

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
	'region' => 'us-west-1'
));

$messageJSON = file_get_contents("php://input");
$message = json_decode($messageJSON,true);

$imageID = $message["imageID"];
$imageURL = $message["image"];
$annotations = $message["annotations"];

try {
	// Create image
	$image = imagecreatefromjpeg($imageURL);

	// Create color
	$yellow = imagecolorallocate($image, 255, 243, 96);

	// Loop through annotations
	foreach($annotations as $annotation){
		// Check for rectangle or line annotation
		if ($annotation[0] == 'rect'){
			// Draw rectangle for rectangle annotations
			imagerectangle($image, $annotation[1], $annotation[2], $annotation[3], $annotation[4], $yellow);
		} else {
			// Draw line for line annotations
			imageline($image, $annotation[1], $annotation[2], $annotation[3], $annotation[4], $yellow);
		}
	}

	imagejpeg($image,"/tmp/processedImage_".$imageID.".jpg",90);

	$upload = $s3Client->putObject(array(
		'Bucket' => 'bah-reinvent-processed-images',
		'Key'    => $imageID.".jpg",
		'Body'   => file_get_contents("/tmp/processedImage_".$imageID.".jpg"),
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