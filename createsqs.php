<?php
require 'vendor/autoload.php';
use Aws\Sqs\SqsClient;
$client = SqsClient::factory(array(
'region'  => 'us-east-1'
));
$result = $client->createQueue(array(
    // QueueName is required
    'QueueName' => 'ak101',
));
echo $result;
?>
