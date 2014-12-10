<html>
<head>
<title>Index</title>
<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>index.php</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

</head>
<body>
<center>
<h1>New Upload Form</h1>
<form enctype="multipart/form-data" action="result.php" method="POST">

<div class="form-group">

                <label for="name">Name</label>
                <input class="form-control" id="name" name="name" type="text" style="width: 300px;" value=""/>
</div>

<div class="form-group">
        <label for="pnum">Phone Number (Format: 12223334444)</label>
        <input class="form-control" id="pnum" name="pnum" type="nnumber" style="width: 300px;" value="" />
</div>

<div class="form-group">
        <label for="cityName">Email id</label>
        <input class="form-control" id="email" name="email" type="text" style="width: 300px;" value=""/>
</div>


<!-- The data encoding type, enctype, MUST be specified as below -->
    <!-- MAX_FILE_SIZE must precede the file input field -->
<!--
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" /> 
-->
    <!-- Name of input element determines name in $_FILES array -->
<div class="form-group">
        <label for="cityName">Browse to select a file to upload and send.</label>
        <input name="image" type="file" />
</div>
<div class="form-group">
        <input type="submit" name="upload" value="Submit Form" />
</div>
</form>
<center>
</body>
</html>
