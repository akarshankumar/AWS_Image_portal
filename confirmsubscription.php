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
$topicarn = '{snsurl}';
$smsendpoint=$_REQUEST['phone'];
$emailendpoint=$_REQUEST['email'];
echo "<h2>Subscription Result.</h2>"."\n";
echo"</br>";
$result = $client->listSubscriptionsByTopic(array(
    // TopicArn is required
    'TopicArn' => $topicarn,
    //'NextToken' => 'string',
));
foreach($result['Subscriptions'] as $sub){
//echo $sub['Endpoint']."\n";
if($sub['Endpoint'] == $smsendpoint)
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
if($sub['Endpoint'] == $emailendpoint)
{
$emailsubscribeflag =1;
break;
} else
{
$emailsubscribeflag =0;
}
}

if(!$smssubscribeflag){
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
echo "$smsendpoint is already subscribed."."\n";
echo"</br>";
}

if(!$emailsubscribeflag){
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
echo "$emailendpoint is already subscribed."."\n";
echo"</br>";
}

echo "<a href=\"/welcome.php\">Click here </a> to back to home page";
?>
