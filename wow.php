<html>
<head>
<title>WOW</title>
<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>WOW</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

</head>
<body>
<?php
echo "Search Parameters: ";
echo "<br>";
echo "Token Id:          " . $_REQUEST['tokenid'];
echo "<br>";
echo "User Email ID:       " . $_REQUEST['email'];
echo "<br>";
echo "<br />  ";

$link = mysqli_connect("{dburl}","{dbusername}","{dbpassword}") or die("Error " . mysqli_error($link));
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$email = $_REQUEST['email'];
$id = $_REQUEST['tokenid'];
if(!($link->real_query("SELECT * FROM rds_db.resource where id='$id' and email='$email'"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
$res = $link->use_result();

while ($row = $res->fetch_assoc()) {
    if (!$row){
        echo "<br />  ";
        echo "Empty Row";
        }
    $uniqid = $row['ID'];
    $uploadfile = $row['posturl'];
    $datatype = $row['datatype'];
}


$link->close();
echo "Search Result:";
echo "</br>";
if ($uniqid){
if (strpos($datatype,'image') !== false) {
echo "<img src=\"$uploadfile\" alt=\"picture\" >";
echo"</br>";
echo "<a href=\"$uploadfile\">Raw Image link</a>";
}else {
        if (strpos($datatype,'video') !== false) {
	echo "<video width=\"320\" height=\"240\" controls>";
	echo "<source src=\"movie.mp4\" type=\"video/mp4\">";
	echo "Your browser does not support the video tag.";
	echo "</video>";
	} else {
	echo "No image found for given values.";
	}	
}
}
?>

</body>
</html>
