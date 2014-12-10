<HTML>
<HEAD> 
<title>Stop Poll 
</title>
</HEAD>
<body>
<?php
sleep(15);
if(file_exists("/var/www/uploads/start.txt")){
echo "Worker BOT found running.\n";
rename("/var/www/uploads/start.txt","/var/www/uploads/stop.txt");
echo "</br>";
echo "Worker BOT will be stopped now!";
} else {
echo "Worker BOT may already been stopped!";
}
?>
<p>
<a href="/admin.php">Go back to Admin Console</a>
</body>
</html>
