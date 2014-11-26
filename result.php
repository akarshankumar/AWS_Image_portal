<html>
<head>
<title>Result</title>
<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>index.php</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

        <!-- Optional theme -->
debug2: channel 0: window 999074 sent adjust 49502n.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">


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
echo "<br />  ";

require 'vendor/autoload.php';

use Aws\S3\S3Client;

$client = S3Client::factory();

if(isset($_POST['upload']))
{
if($_FILES['image']['name']==''){
echo "Alert : No file selected!";
exit();
}
else
move_uploaded_file($image_tmp_name,"/var/www/uploads/temp_image");
echo "File is valid and was successully uploaded";
echo "<br />";
echo "Here are some more debugging info:";
echo "<br />";
echo "<br />";
print_r($_FILES);
print "</pre>";
echo "<br />";
}

$bucket = uniqid("php-sdk-sample-", true);
echo "Creating bucket named {$bucket}\n";
$result = $client->createBucket(array(
    'Bucket' => $bucket
));

$client->waitUntilBucketExists(array('Bucket' => $bucket));

$key='myimage.jpg';
$pathToFile='/var/www/uploads/temp_image';
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
echo "Public URL to access image file is: ";
echo $uploadfile = $result['ObjectURL'];
echo "<br />";
?>
<img src="<?php echo $uploadfile; ?>" alt="picture" >
</body>
</html>
