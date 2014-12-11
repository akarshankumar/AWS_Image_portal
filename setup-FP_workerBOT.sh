#!/bin/bash

dburl="itmo544ak.calpvw9gcdle.us-east-1.rds.amazonaws.com"
dbusername="controller"
dbpassword="ilovetota"
sqsurl="https://sqs.us-east-1.amazonaws.com/223848885127/akumar25Q"
bucketname="itmo544s3ak"
snsurl="arn:aws:sns:us-east-1:223848885127:akumar25SNS"

sudo apt-get -y update  1>/tmp/1_update.out 2>/tmp/1_update.err
sudo apt-get -y upgrade 1>/tmp/8_upgrade.out 2>/tmp/8_upgrade.err

sudo apt-get -y install apache2 wget php5 curl git 1>/tmp/2_apache.out 2>/tmp/2_apache.err
sudo apt-get install php5-curl 1>/tmp/9_php5curl.out 2>/tmp/9_php5curl.err
sudo apt-get install -y php5-mysql 1>/tmp/10_php5mysql.out 2>/tmp/10_php5mysql.err

export DEBIAN_FRONTEND=noninteractive
sudo -E apt-get -q -y install mysql-server  1>/tmp/11_mysqlserver.out 2>/tmp/11_mysqlserver.err

sudo apt-get install -y php5-gd 1>/tmp/14_php5gd.out 2>/tmp/14_php5gd.err

sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/admin.php 1>/tmp/3_adminphp.out 2>/tmp/3_adminphp.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/stop.php 1>/tmp/4_stopphp.out 2>/tmp/4_stopphp.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/workerbot.php 1>/tmp/12_workerbotphp.out 2>/tmp/12_workerbotphp.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/stop.txt 1>/tmp/13_startext.out 2>/tmp/13_starttext.err

sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/composer.json 1>/tmp/5_composer.out 2>/tmp/5_composer.err

sudo curl -sS https://getcomposer.org/installer | sudo php 1>/tmp/6_php.out 2>/tmp/6_php.err
sudo php composer.phar install 1>/tmp/7_phar.out 2>/tmp/7_phar.err

sudo sed -i "s,{dburl},${dburl},g" workerbot.php
sudo sed -i "s,{dbusername},${dbusername},g" workerbot.php
sudo sed -i "s,{dbpassword},${dbpassword},g" workerbot.php
sudo sed -i "s,{sqsurl},${sqsurl},g" workerbot.php
sudo sed -i "s,{snsurl},${snsurl},g" workerbot.php
sudo sed -i "s,{bucketname},${bucketname},g" workerbot.php

mkdir /var/www/uploads
chmod 777 /var/www/uploads

mv /admin.php /var/www/html
mv /stop.php /var/www/html
mv /workerbot.php /var/www/html
mv /stop.txt /var/www/uploads/

mv /composer.json /var/www/html
mv /vendor /var/www/html

mkdir /var/www/uploads
chmod 777 /var/www/uploads

sudo restart apache2
sudo apache2ctl graceful

sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/fstab
sudo mv fstab /etc/
sudo mount -a
