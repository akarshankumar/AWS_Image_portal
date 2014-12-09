<html>
<head>
<title> DbCreate </title>
</head>
<body>

<?php
//conection: 

echo "Creating DataBase.";
echo "</br>";


if (!function_exists('mysqli_init') && !extension_loaded('mysqli')) {
    echo 'We don\'t have mysqli!!!\n';
}
$link = mysqli_connect("{dburl}","{username}","{password}") or die("Error " . mysqli_error($link));

$sql = 'CREATE DATABASE rds_db';
if (mysqli_query($link, $sql)) {
    echo "Database rds_db created successfully\n";
} else {
    echo 'Error creating database: ' . mysqli_error($link) . "\n";
echo "</br>";
}

mysqli_select_db($link, 'rds_db') or die(mysqli_error($link));

$query = "describe rds_db.resource";
$result = mysqli_query($link, $query);

if(empty($result)) {
$sql = "CREATE TABLE IF NOT EXISTS resource 
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(32),
filename varchar(100),
rawurl VARCHAR(200),
posturl VARCHAR(200),
datatype VARCHAR(20),
phone VARCHAR(32),
email VARCHAR(500),
CREATION_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
)";

$result = $link->query($sql);
echo "Table Created";
echo "</br>";
echo $sql;
} else {
echo "Cannot create Table: Table already exists";
}



?>
</body>
</html>
         
