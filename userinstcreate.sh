#!/bin/bash

if [ $# != 6 ]
then 
echo "Syntax: ./akcreatelbFP.sh  KEYPAIR CLIENT_TOKENS, NUMBER_OF_INSTANCES  SECURITY_GROUP_NAME LOAD_BALANCER_NAME IAM_ROLE";exit;
else
{
echo "------------------------------------------------" >> cloud.properties
#use the aws.amazon.com/cli reference EXTENSIVELY for this - you won't find it via google - hunker down
#Step 1: Create a VPC with a /28 cidr block (see the aws example) - assign the vpc-id to a variable  you can awk column $6 on the --output=text to get the value
echo "*************************************************"
echo "1. Creating VPC "
vpcval=`aws ec2 create-vpc  --cidr-block 172.31.0.0/16  --output=text | awk '{print $6}'`
echo $vpcval
echo "VPCVAL=$vpcval" >> cloud.properties

#Step 2: Create a subnet for the VPC - use the same /28 cidr block that you used in step 1.  Save the subnet-id to a variable (retrieve it by awk column 6)
echo "2. Creating subnet "
subnet=`aws ec2 create-subnet --vpc-id $vpcval --cidr-block 172.31.0.0/16 --output=text --availability-zone us-east-1c| awk '{print $6}'`
echo $subnet
echo "SUBNETVAL=$subnet" >> cloud.properties


#Step 3: Create a custom security group per this VPC - store the group ID in a variable (awk $1) 
echo "3. Creating Security Group "
SGID=`aws ec2 create-security-group --group-name $4 --description "My security group" --vpc-id $vpcval  --output=text | awk '{print $1}'`
echo $SGID
echo "SGID=$SGID" >> cloud.properties

#step 3b:  Open the ports For SSH and WEB access to your security group ( this one I give you)
echo "3b. Enabling ports for SG"
aws ec2 authorize-security-group-ingress --group-id $SGID --protocol tcp --port 80 --cidr 0.0.0.0/0 --output=text
aws ec2 authorize-security-group-ingress --group-id $SGID --protocol tcp --port 22 --cidr 0.0.0.0/0 --output=text


#Step 4: We need to create an internet gateway so that our VPC has internet access - save the gaetway ID to a vaiable (awk column 2) 
echo "4. Creating IGW "
igway=`aws ec2 create-internet-gateway --output=text | awk '{print $2}'`
echo $igway
echo "INTERNETGWAY=$igway" >> cloud.properties

#step 4b:  We need to modify the VPC attributes to enable dns support and enable dns hostnames - see the examples note that you cannot combine these options you have to make two modify entries
echo "4b. Enabling DNS support and DNS Hostname."
aws ec2 modify-vpc-attribute --vpc-id $vpcval --enable-dns-support "{\"Value\":true}" --output=text
aws ec2 modify-vpc-attribute  --vpc-id $vpcval --enable-dns-hostnames "{\"Value\":true}" --output=text

#Step 5 Modify-subnet-attribute - tell the subnet id to --map-public-ip-on-launch 
echo "5. Modifying subnet attribute "
aws ec2 modify-subnet-attribute  --subnet-id $subnet --map-public-ip-on-launch --output=text

#Step 6:  We need to attach the internet gateway we created to our VPC
echo "6. Attaching IGW to VPC "
aws ec2 attach-internet-gateway --internet-gateway-id $igway --vpc-id $vpcval --output=text

#Step 6b: Now lets create a ROUTETABLE variable and use the command create-route-table command to get the routetable id us  | grep rtb | awk {'print $2'}
echo "Creating ROUTETABLE "
rtb=`aws ec2 create-route-table --vpc-id $vpcval --output=text | grep rtb | awk {'print $2'}`
echo $rtb
echo "ROUTETABLE=$rtb" >> cloud.properties

#Step 6c: Now we create a route to be attached to the route table (I know its kind of verbose but this is what the GUI is doing automatically)  --destination-cidr-block is 0.0.0.0/0 
echo "Create route to attache route table with IGW"
aws ec2 create-route --route-table-id $rtb --destination-cidr-block 0.0.0.0/0 --gateway-id $igway --output=text

#Step 6d:  Now associate that route with a route-table-id and a subnet-id
echo "Associating routing table with subnet"
rtbassoc=`aws ec2 associate-route-table --route-table-id $rtb --subnet-id $subnet --output=text`

#Step 7:  Now create a ELBURL variable and lets create a load balancer - change from the EC2 cli docs to the ELB docs.  Use the default example --listeners from the VPC section (not classic EC2 routing)) I am leaving some formatting code in here that will print '.' for a time to give the system time to finish registering 
echo "Creating Load Balancer"
ELBURL=`aws elb create-load-balancer --load-balancer-name $5 --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --subnets $subnet --security-groups $SGID --output=text`
echo $ELBURL
echo "ELBURL=$ELBURL" >> cloud.properties

echo -e "\nFinished launching ELB and sleeping 25 seconds"
for i in {0..25}; do echo -ne `expr 25 - $i`' ' ; sleep 1;done
echo -e "\n"

#step 7b: This is the elb configure-health-check this section is what the loadbalancer will be checking and how often - use the code that is in the example and check in HTTP:80/index.html
aws elb configure-health-check --load-balancer-name $5 --health-check Target=HTTP:80/index.html,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3

echo -e "\nFinished ELB health check and sleeping 30 seconds"
for i in {0..30}; do echo -ne `expr 30 - $i`' ' ; sleep 1;done
echo -e "\n"

#step 7c: This creates a sticky session in a load balancer to validate request and response are being catered by same backend machine
aws elb create-lb-cookie-stickiness-policy --load-balancer-name $ELBURL --policy-name MyDurationStickyPolicy --cookie-expiration-period 300

#echo "Creating key pair"
#aws ec2 create-key-pair --key-name $2 > $2

echo "Creating instances."
#Step 8: Here is where we launch our instances, provide the VPC configuration, provide client-tokens, and provide the user-data via the file:// handler setup-MA3.sh -- See EC2 docs run-instances example for VPC launch (Good thing you saved those id's into variable so you could access them later in the script.)
aws ec2 run-instances --image-id ami-30a42358 --count $3 --region us-east-1 --instance-type t1.micro --key-name $1 --security-group-ids $SGID --subnet-id $subnet --associate-public-ip-address --client-token $2 --output=text --user-data file://setup-FP.sh --iam-instance-profile Name=$6 --block-device-mappings "[{\"DeviceName\": \"/dev/sdh\",\"Ebs\":{\"VolumeSize\":10}}]"


echo -e "\nFinished launching EC2 Instances and sleeping 60 seconds"
for i in {0..60}; do echo -ne `expr 60 - $i`' ' ; sleep 1;done
echo -e "\n"

#Step 9: Here we declare an array in BASH and list our instances - then we use the --filters Name=client-token,Values=(your value here)   --output=text | grep INSTANCES | awk {'print $*'}  that should get your the instance-ids
declare -a ARRAY 
ARRAY=(`aws ec2 describe-instances --filters Name=client-token,Values=$2 --output text | grep INSTANCES | awk {' print $8'}`)
echo -e "\nListing Instances, filtering their instance-id, adding them to an ARRAY and sleeping 15 seconds"
for i in {0..15}; do echo -ne `expr 15 - $i`' ' ; sleep 1;done
echo -e "\n"



#Step 10: Here the first line calculates the length of the array $# is a system variable that know its length.   Now we loop through the instance array and add each instance to our loadbalancer one by one and print out the progress. I give this one to you 
LENGTH=${#ARRAY[@]}
echo "ARRAY LENGTH IS $LENGTH"
for (( i=0; i<${LENGTH}; i++)); 
  do
  echo "Registering ${ARRAY[$i]} with load-balancer $5, count $i" 
  aws elb register-instances-with-load-balancer --load-balancer-name $5 --instances ${ARRAY[$i]} --output=table 
echo -e "\nLooping through instance array and registering each instance one at a time with the load-balancer.  Then sleeping 60 seconds to allow the process to finish. )"
    for y in {0..60} 
    do
      echo -ne '.'
      sleep 1
    done
 echo "\n"
done
echo "out of loop $i"

echo -e "\nWaiting an additional 3 minutes (180 second) - before opening the ELB in a webbrowser"
for i in {0..180}
 do 
   echo -ne `expr 180 - $i`' '
  sleep 1
 done
echo -e "\n"

#Last Step
firefox $ELBURL/index.php &
}
fi
 
#End of if statement





