<?php
require 'vendor/autoload.php';
use Aws\Sns\SnsClient;
$snsclient = SnsClient::factory(array(
'region'  => 'us-east-1'
));

$result = $snsclient->createTopic(array(
    // Name is required
    'Name' => '101',
));

echo $result;

?>
