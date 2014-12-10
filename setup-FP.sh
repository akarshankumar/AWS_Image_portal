#!/bin/bash
# Written By: Akarshan Kumar
# Scriptname: score
# Purpose: This script is submission for ITMO544.
#          This script is input file for instances, when they are created in AWS
# Usage  --user-data file://setup-FP.sh

# Don't remove quotes, replace values inside <> alongwith angular brackets.
dburl="<Give your RDS DB URL>"
dburlread="<Give the RDS read replica URL>"
dbusername="<Give your DB username>"
dbpassword="Give your DB password"
sqsurl="<Give your SQS URL>"
bucketname="<Give just your bucket name not the complete URL>"
snsurl="<Give sns URL here>"
sudo apt-get -y update  1>/tmp/1_update.out 2>/tmp/1_update.err
sudo apt-get -y upgrade 1>/tmp/8_upgrade.out 2>/tmp/8_upgrade.err

sudo apt-get -y install apache2 wget php5 curl git 1>/tmp/2_apache.out 2>/tmp/2_apache.err
sudo apt-get install php5-curl 1>/tmp/9_php5curl.out 2>/tmp/9_php5curl.err
sudo apt-get install -y php5-mysql 1>/tmp/10_php5mysql.out 2>/tmp/10_php5mysql.err

export DEBIAN_FRONTEND=noninteractive
sudo -E apt-get -q -y install mysql-server  1>/tmp/11_mysqlserver.out 2>/tmp/11_mysqlserver.err


sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/index.php 1>/tmp/3_index.out 2>/tmp/3_index.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/result.php 1>/tmp/4_result.out 2>/tmp/4_result.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/composer.json 1>/tmp/5_composer.out 2>/tmp/5_composer.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/welcome.php 1>/tmp/12_welcomephp.out 2>/tmp/12_welcomephp.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/search.php 1>/tmp/13_searchphp.out 2>/tmp/13_searchphp.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/wow.php 1>/tmp/14_wowphp.out 2>/tmp/14_wowphp.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/subscribe.php 1>/tmp/15_subscribephp.out 2>/tmp/14_subscribephp.err
sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/confirmsubscription.php 1>/tmp/16_confirmsubscriptionphp.out 2>/tmp/16_confirmsubscriptionphp.err

sudo curl -sS https://getcomposer.org/installer | sudo php 1>/tmp/6_php.out 2>/tmp/6_php.err
sudo php composer.phar install 1>/tmp/7_phar.out 2>/tmp/7_phar.err

sudo sed -i "s,{dburl},${dburl},g" result.php
sudo sed -i "s,{dbusername},${dbusername},g" result.php
sudo sed -i "s,{dbpassword},${dbpassword},g" result.php
sudo sed -i "s,{sqsurl},${sqsurl},g" result.php
sudo sed -i "s,{bucketname},${bucketname},g" result.php

sudo sed -i "s,{dburlread},${dburlread},g" wow.php
sudo sed -i "s,{dbusername},${dbusername},g" wow.php
sudo sed -i "s,{dbpassword},${dbpassword},g" wow.php

sudo sed -i "s,{snsurl},${snsurl},g" confirmsubscription.php

mv /index.php /var/www/html
mv /result.php /var/www/html
mv /welcome.php /var/www/html
mv /search.php /var/www/html
mv /wow.php /var/www/html

mv /composer.json /var/www/html
mv /vendor /var/www/html

mkdir /var/www/uploads
chmod 777 /var/www/uploads

sudo restart apache2
sudo apache2ctl graceful

sudo wget https://raw.githubusercontent.com/akarshankumar/itm544/master/fstab
sudo mv fstab /etc/
sudo mount -a
