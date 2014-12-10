<html>
<head>
<title>Result</title>
<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>File Upload</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>
<?php
echo "Retrived Form Data";
echo "<br>";
echo "User Name:          " . $_REQUEST['name'];
echo "<br>";
echo "User Phone Number:  " . $_REQUEST['pnum'];
echo "<br>";
echo "User Email ID       " . $_REQUEST['email'];
echo "<br>";

require 'vendor/autoload.php';

use Aws\S3\S3Client;

$client = S3Client::factory();

if(isset($_POST['upload']))
{
if($_FILES['image']['name']==''){
echo "Alert : No file selected!";
exit();
}
else {
echo "Temp file  name: " + $_FILES['image']['tmp_name'];
}

if (strpos($_FILES['image']['type'],'image') !== false) {
    $temp_path = "/var/www/uploads/temp_image";
    $file_type = "image";
} else {

	if (strpos($_FILES['image']['type'],'video') !== false) {
	    $temp_path = "/var/www/uploads/temp_video";
    	    $file_type = "video";
	} else {
	echo "Invalid File Type";
	}	
}

echo '<pre>';
if (move_uploaded_file($_FILES['image']['tmp_name'], $temp_path)) {
    echo "File is valid, and was successfully uploaded.\n";
	echo "<br />";
	echo "Here are some more debugging info:";
	echo "<br />";
	echo "<br />";
	print_r($_FILES);
	print "</pre>";
	echo "<br />";

} else {
    echo "Possible file upload attack!\n";
}

}
$bucket = "{bucketname}";

if($client->doesBucketExist($bucket)) {
echo "Bucket exists!";
} else {
echo "Bucket doesn't exist, creating requested bucket.";
$result = $client->createBucket(array(
    'Bucket' => $bucket
));
echo "</br>";
}
$client->waitUntilBucketExists(array('Bucket' => $bucket));

$key = $_FILES['image']['tmp_name'].$_FILES['image']['name'];
$pathToFile=$temp_path;
$result = $client->putObject(array(
    'Bucket'     => $bucket,
    'Key'        => $key,
    'ACL'        => 'public-read',
    'SourceFile' => $pathToFile,
    'Metadata'   => array(
        'Foo' => 'abc',
        'Baz' => '123'
    )
));
$client->waitUntil('ObjectExists', array(
    'Bucket' => $bucket,
    'Key'    => $key
));

echo "<br />";
echo "Public URL to access file is: ";
$uploadfile = $result['ObjectURL'];
echo $uploadfile;
echo "<br />";

echo "Connecting to database.";
echo "</br>";
$link = mysqli_connect("{dburl}","{dbusername}","{dbpassword}") or die("Error " . mysqli_error($link));
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


   # id INT NOT NULL AUTO_INCREMENT,
   # name VARCHAR(200) NOT NULL,
   # age INT NOT NULL,

/* Prepared statement, stage 1: prepare */
/* Prepared statement, stage 1: prepare */
if (!($sql = $link->prepare("INSERT INTO rds_db.resource (id, username,rawurl,phone,email,filename,datatype) VALUES (NULL,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

$id = 1;
$name = $_REQUEST['name'];
$phone = $_REQUEST['pnum'];
$email = $_REQUEST['email'];
$fname = preg_replace('-\W-','',date('m-d-Y H:i:s A e')).$_FILES['image']['name'];
echo "File Name" . $fname;
$sql->bind_param("ssssss",$_REQUEST['name'],$uploadfile, $_REQUEST['pnum'],$_REQUEST['email'],$fname,$_FILES['image']['type']);

if (!$sql->execute()) {
    echo "Execute failed: (" . $sql->errno . ") " . $sql->error;
echo "<br />  ";
}

echo "<br />  ";
printf("%d Row inserted.\n", $sql->affected_rows);


/* explicit close recommended */
$sql->close();

if(!($link->real_query("SELECT * FROM rds_db.resource where rawurl='$uploadfile'"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
$res = $link->use_result();

echo "<br />  ";
echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    if (!$row){
        echo "<br />  ";
        echo "Empty Row";
        }
    echo "<br />  ";
    echo " id = " . $row['ID'];
    echo " Name = " . $row['username'];
    echo " RawURL = " . $row['rawurl'];
    echo " Phone = " . $row['phone'];
    echo " email = " . $row['email'];
    $uniqid = $row['ID'];
}
$link->close();
?>

<?php

use Aws\Sqs\SqsClient;

$client = SqsClient::factory(array(
'region'  => 'us-east-1'
));
echo "</br>";
echo "Row ID: " . $uniqid;

$result = $client->sendMessage(array(
    // QueueUrl is required
    'QueueUrl' => '{sqsurl}',
    // MessageBody is required
    'MessageBody' => $uniqid,
    'DelaySeconds' => 30
));

echo "</br>";
if ($file_type == 'image'){
	echo "<img src=\"$uploadfile\" alt=\"picture\" >";
}

if ($file_type == 'video'){
	echo "<video width=\"320\" height=\"240\" controls>";
echo "<source src=\"movie.mp4\" type=\"video/mp4\">";
echo "Your browser does not support the video tag.";
echo "</video>";
}

echo "<form enctype=\"multipart/form-data\" action=\"confirmsubscription.php\" method=\"POST\">";
echo "<h5>Click on subscribe button to recieve notifications.</h5>";
echo "<div class=\"form-group\">";
echo " <input type=\"hidden\" name=\"email\" value=\"$email\">";
echo " <input type=\"hidden\" name=\"phone\" value=\"$phone\"> ";
echo "<input type=\"submit\" name=\"subscribe\" value=\"Subscribe\" />";
echo "</div>";
echo "</form>";


?>
</body>
</html>
