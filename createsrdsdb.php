<?php
require 'vendor/autoload.php';

use Aws\Rds\RdsClient;
$client = RdsClient::factory(array(
'region' => 'us-east-1'
));
$result = $client->createDBInstance(array(
    'DBName' => 'akMySQL',
    // DBInstanceIdentifier is required
    'DBInstanceIdentifier' => 'aktestDB2',
    // AllocatedStorage is required
    'AllocatedStorage' => 5,
    // DBInstanceClass is required
    'DBInstanceClass' => 'db.t1.micro',
    // Engine is required
    'Engine' => 'MySQL',
    // MasterUsername is required
    'MasterUsername' => 'controller',
    // MasterUserPassword is required
    'MasterUserPassword' => 'ilovetota',
    //'DBSecurityGroups' => array('Q-SG' ),
    'VpcSecurityGroupIds' => array('sg-417e6224'),
    'AvailabilityZone' => 'us-east-1b',
    'DBSubnetGroupName' => 'default',
    'PreferredMaintenanceWindow' => 'sat:10:28-sat:10:58',
    'BackupRetentionPeriod' => 1,
    'PreferredBackupWindow' => '09:47-10:17',
    'Port' => 3306,
    'MultiAZ' => false,
    'EngineVersion' => '5.6.19a',
    'AutoMinorVersionUpgrade' => true,
    'LicenseModel' => 'general-public-license',
    'OptionGroupName' => 'default:mysql-5-6',
    //'CharacterSetName' => 'UTF-8',
    'PubliclyAccessible' => false,
    'Tags' => array(
        array(
            'Key' => 'foo',
            'Value' => 'bar',
        )
    ),
    'StorageType' => 'standard',
));

$dbinstidentifier= $result['DBInstanceIdentifier'];
echo "DBInstanceIdentifier is ".$dbinstidentifier;
$myfile = fopen("../uploads/db.properties", "w") or die("Unable to open file!");
$txt = "DBInstanceIdentifier=".$dbinstidentifier;
fwrite($myfile, $txt);

//20 mins delay, before replica is started.
sleep(1200);

$result = $client->createDBInstanceReadReplica(array(
    // DBInstanceIdentifier is required
    'DBInstanceIdentifier' => 'aktestdbreplica',
    // SourceDBInstanceIdentifier is required
    'SourceDBInstanceIdentifier' => $dbinstidentifier,
    'DBInstanceClass' => 'db.t2.micro',
    'AvailabilityZone' => 'us-east-1b',
    'Port' => 3306,
    'AutoMinorVersionUpgrade' => true,
    'OptionGroupName' => 'default:mysql-5-6',
    'PubliclyAccessible' => true,
    'Tags' => array(
        array(
            'Key' => 'foo',
            'Value' => 'bar',
        ),
        // ... repeated
    ),
    'DBSubnetGroupName' => 'default',
    'StorageType' => 'standard',
));

$dbinstidentifierreplica=$result['DBInstanceIdentifier'];
echo "DBInstanceIdentifier is ".$dbinstidentifierreplica;
$txt = "replicaDBInstanceIdentifier=".$dbinstidentifierreplica;
fwrite($myfile, $txt);

fclose($myfile);
?>
