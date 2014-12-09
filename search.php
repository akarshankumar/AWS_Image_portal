<html>
<head>
<!-- Created By Akarshan Kumar for submission to ITMO544, final project, this page helps user to search image -->
<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Search Image</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
</head>
<body>
<center>
<h1>Search File</h1>
<form enctype="multipart/form-data" action="wow.php" method="POST">
<div class="form-group">
                <label for="tokenid">Token Id</label>
                <input class="form-control" id="tokenid" name="tokenid" type="number" style="width: 300px;" value=""/>
</div>
<div class="form-group">
        <label for="cityName">Email id</label>
        <input class="form-control" id="email" name="email" type="text" style="width: 300px;" value=""/>
</div>
<div class="form-group">
        <input type="submit" name="submit" value="Search" />
</div>
</form>
<center>
</body>
</html>
