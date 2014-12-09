<html>
<head>
<title> Worker BOT </title>
</head>
<body>

<?php
/*
 * Copyright 2013. Amazon Web Services, Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
**/

// Include the SDK using the Composer autoloader

// For SQS queue for the messages
require 'vendor/autoload.php';
use Aws\Sqs\SqsClient;
$client = SqsClient::factory(array(
'region'  => 'us-east-1'
));

// For S3 buckets to store images
require 'vendor/autoload.php';
use Aws\S3\S3Client;
$s3client = S3Client::factory();


//For sending messages to end user through SNS
require 'vendor/autoload.php';
use Aws\Sns\SnsClient;
$snsclient = SnsClient::factory(array(
'region'  => 'us-east-1'
));
/*
 If you instantiate a new client for Amazon Simple Storage Service (S3) with
 no parameters or configuration, the AWS SDK for PHP will look for access keys
 in the AWS_ACCESS_KEY_ID and AWS_SECRET_KEY environment variables.

 For more information about this interface to Amazon S3, see:
 http://docs.aws.amazon.com/aws-sdk-php-2/guide/latest/service-s3.html#creating-a-client
*/

function make_thumb($src, $dest, $desired_width) {

        /* read the source image */
        $source_image = imagecreatefromjpeg($src);
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($desired_width / $width));

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

        /* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $dest);
}

//set_time_limit(2);
//$client = SqsClient::factory(array(
//'region'  => 'us-east-1'
//));

$SqsURL = "{sqsurl}";

//Checking if stop.txt is already present or not, if present then rename it
if(file_exists("/var/www/uploads/stop.txt")){
echo "Stop file exists, renaming it.\n";
rename("/var/www/uploads/stop.txt","/var/www/uploads/start.txt");
} else {
exit("Worker BOT may already be running!");
}

echo "Woker BOT Started";
sleep (5);
while (!file_exists("/var/www/uploads/stop.txt"))
{
$messageBody = "";
$receiptHandle = "";
$result = $client->receiveMessage(array(
    // QueueUrl is required
    'QueueUrl' => $SqsURL,
    'MaxNumberOfMessages' => 1,
    'VisibilityTimeout' => 30,
//    'WaitTimeSeconds' => 60,
));


foreach ($result->getPath('Messages/*/Body') as $messageBody) {
    // Do something with the message
//    echo "Message Body: " . $messageBody ."\n";

}
echo "</br>";
echo "</br>";
echo "</br>";
foreach ($result->getPath('Messages/*/ReceiptHandle') as $receiptHandle) {
    // Do something with the message
    //echo $receiptHandle ."\n";
}

$result = $client->deleteMessage(array(
    // QueueUrl is required
    'QueueUrl' => $SqsURL,
    // ReceiptHandle is required
    'ReceiptHandle' => $receiptHandle,
));

//echo "Message has been deleted!";


echo "</br>";
//echo "Connecting to database.";
echo "</br>";
$link = mysqli_connect("{dburl}","{dbusername}","{dbpassword}") or die("Error " . mysqli_error($link));
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if(!($link->real_query("SELECT * FROM rds_db.resource where id='$messageBody'"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
$res = $link->use_result();

//echo "<br />  ";
echo "<br />  ";
//echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    if (!$row){
        echo "<br />  ";
        echo "Empty Row";
        }
    //echo "<br />  ";
    //echo " id = " . $row['ID'];
    //echo " Name = " . $row['username'];
    //echo " RawURL = " . $row['rawurl'];
    //echo " Phone = " . $row['phone'];
    //echo " email = " . $row['email'];
	$uniqid = $row['ID'];
	$rawurl = $row['rawurl'];
	$fname =  $row['filename'];
	$datatype = $row['datatype'];
}

echo "<br />  ";
echo    $uniqid;
echo "<br />  ";
echo    $rawurl;
echo "<br />  ";
echo    $fname ;
echo "<br />  ";
echo "Data Type: $datatype";
echo "<br />  ";
//$posturl = str_replace("tmp","post",$rawurl);
if (strpos("x".$datatype,'image') !== false) {
    $posturl = '/var/www/uploads/test_image.jpeg';
    $file_type = 'image';
} else {

        if (strpos($datatype,'video') !== false) {
            $temp_path = "/var/www/uploads/temp_video";
    	    $posturl = '/var/www/uploads/' . $fname;
            $file_type = 'video';
        } else {
        echo "Invalid File Type";
       $posturl = '/var/www/uploads/test_image.jpeg';
        }
}
echo "Data type verified";
$posturl = '/var/www/uploads/test_image.jpeg';
//echo "</br>";
//echo "Post URL: " . $posturl;
if ($file_type == 'image') {
make_thumb($rawurl, $posturl, 100);
}
//:w$s3client = S3Client::factory();

$bucket = "{bucketname}";
$s3client->waitUntilBucketExists(array('Bucket' => $bucket));
$key = "/post/" . $fname;

//$pathToFile='/var/www/uploads/test_image.jpeg';
$s3result = $s3client->putObject(array(
    'Bucket'     => $bucket,
    'Key'        => $key,
    'ACL'        => 'public-read',
    'SourceFile' => $posturl,
    'Metadata'   => array(
        'Foo' => 'abc',
        'Baz' => '123'
    )
));
$s3client->waitUntil('ObjectExists', array(
    'Bucket' => $bucket,
    'Key'    => $key
));

echo "<br />";
//echo "Public URL to access image file is: ";
$uploadfile = $s3result['ObjectURL'];
//echo $uploadfile;
//echo "<br />";
//echo "<br />";

//echo "<img src=\"$uploadfile\" alt=\"picture\" >";
//echo "<br />";
//echo date('h:i:s') . "<br>";
//sleep(15);
//echo date('h:i:s') . "<br>";
//echo "<br />";
//echo "<br />";

$result = $snsclient->publish(array(
    'TopicArn' => '{snsurl}',
    'Message' => 'Success! your file is hosted.\nPlease visit at following location: ' . $uploadfile,
    'Subject' => 'File is hosted!',
    'MessageStructure' => 'string',
));


if (!($sql = $link->prepare("update rds_db.resource set posturl=? where id = ?"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

$sql->bind_param("ss",$uploadfile,$uniqid);

if (!$sql->execute()) {
    echo "Execute failed: (" . $sql->errno . ") " . $sql->error;
echo "<br />  ";
}

echo "<br />  ";
//printf("%d Row inserted.\n", $sql->affected_rows);


/* explicit close recommended */
$sql->close();
$link->close();

echo "Worker BOT stopped!!";

} 
?>
</body>
</html>
