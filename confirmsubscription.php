<?php

// Include the SDK using the Composer autoloader
require 'vendor/autoload.php';

use Aws\Sns\SnsClient;

/*
 If you instantiate a new client for Amazon Simple Storage Service (S3) with
 no parameters or configuration, the AWS SDK for PHP will look for access keys
 in the AWS_ACCESS_KEY_ID and AWS_SECRET_KEY environment variables.

 For more information about this interface to Amazon S3, see:
 http://docs.aws.amazon.com/aws-sdk-php-2/guide/latest/service-s3.html#creating-a-client
*/
$client = SnsClient::factory(array(
'region'  => 'us-east-1'
));
$topicarn = 'arn:aws:sns:us-east-1:223848885127:akumar25SNS';
$smsendpoint=$_REQUEST['phone'];
$emailendpoint=$_REQUEST['email'];
$smssubscribeflag=1;
$emailsubscribeflag=1;
echo "<h2>Subscription Result.</h2>"."\n";
echo"</br>";
if(!$smsendpoint.trim()){
$smsnull=1;
}

if(!$emailendpoint.trim()){
$emailnull=1;
}


try
{
$result = $client->listSubscriptionsByTopic(array(
    // TopicArn is required
    'TopicArn' => $topicarn,
    //'NextToken' => 'string',
));
foreach($result['Subscriptions'] as $sub){
//echo $sub['Endpoint']."\n";
if(($sub['Endpoint'] == $smsendpoint) || ($smsnull==1))
{
$smssubscribeflag =1;
break;
} else
{
$smssubscribeflag =0;
}
}
foreach($result['Subscriptions'] as $sub){
//echo $sub['Endpoint']."\n";
if(($sub['Endpoint'] == $emailendpoint) || ($emailnull==1))
{
$emailsubscribeflag =1;
break;
} else
{
$emailsubscribeflag =0;
}
}
if($smssubscribeflag==0){
$result = $client->subscribe(array(
    // TopicArn is required
    'TopicArn' => $topicarn,
    // Protocol is required
    'Protocol' => 'sms',
    'Endpoint' => $smsendpoint,
));
echo "Subscription request sent to $smsendpoint"."\n";
echo"</br>";
} else {
if($smsnull!=1){
echo "$smsendpoint is already subscribed."."\n";
}else{
echo "Phone number not provided.";
}
echo"</br>";
}

if($emailsubscribeflag==0){
$result = $client->subscribe(array(
    // TopicArn is required
    'TopicArn' => $topicarn,
    // Protocol is required
    'Protocol' => 'email',
    'Endpoint' => $emailendpoint,
));
echo "Subscription request sent to $emailendpoint"."\n";
echo"</br>";
} else {
if($emailnull!=1){
echo "$emailendpoint is already subscribed."."\n";
}else{
echo "Email id not provided.";
}
echo"</br>";
}
} catch (Exception $e)
{
//if ($e->getMessage() == "The input receipt handle is invalid."){
//echo $e->getMessage();
if($e->getMessage()== "Invalid parameter: Unsupported SMS endpoint: $smsendpoint")
{
echo "Invalid number provided";
}else{
echo $e->getMessage();
}
}
echo "</br><a href=\"/welcome.php\">Click here </a> to back to home page";

?>
