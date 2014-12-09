<html>
<head>
<title>
Admin Console
</title>
</head>
<body>
</br>
<?php
if(file_exists("/var/www/uploads/stop.txt")){
echo "<p>";
echo "<a href=\"/workerbot.php\" target=\"_blank\">Start the Worker BOT.</a>";
echo "</p>";
} else { 
echo "<p>";
echo "<a href=\"/stop.php\">Stop the Worker BOT. (Takes 1 refresh cycle)</a>";
echo "</p>";
}
header("Refresh:2");
?>
</body>
</html>
