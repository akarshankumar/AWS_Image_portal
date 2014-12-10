<html>
<head>
<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Subscribe</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

</head>
<body>
<center>
<h1>New Subscription</h1>
<form enctype="multipart/form-data" action="result.php" method="POST">
<h5>(Please provide email id and phone number to subscribe to to recieve notification.)</h5>
<div class="form-group">
        <label for="pnum">Phone Number</label>
        <input class="form-control" id="pnum" name="pnum" type="text" style="width: 300px;" value="" />
</div>

<div class="form-group">
        <label for="cityName">Email id</label>
        <input class="form-control" id="email" name="email" type="text" style="width: 300px;" value=""/>
</div>


<div class="form-group">
        <input type="submit" name="subscribe" value="Subscribe" />
</div>
</form>
<center>
</body>
</html>
