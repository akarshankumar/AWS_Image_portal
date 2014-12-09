#!/bin/bash

sudo apt-get -y update  1>/tmp/1_update.out 2>/tmp/1_update.err
sudo apt-get -y install apache2 wget php5 curl git 1>/tmp/2_apache.out 2>/tmp/2_apache.err
sudo apt-get install php5-curl

sudo wget ec2-54-191-137-35.us-west-2.compute.amazonaws.com/index.php 1>/tmp/3_index.out 2>/tmp/3_index.err
sudo wget ec2-54-191-137-35.us-west-2.compute.amazonaws.com/result.txt 1>/tmp/4_result.out 2>/tmp/4_result.err
sudo wget ec2-54-191-137-35.us-west-2.compute.amazonaws.com/composer.json 1>/tmp/5_composer.out 2>/tmp/5_composer.err

sudo curl -sS https://getcomposer.org/installer | sudo php 1>/tmp/6_php.out 2>/tmp/6_php.err
sudo php composer.phar install 1>/tmp/7_phar.out 2>/tmp/7_phar.err

mv /index.php /var/www/html
mv /composer.json /var/www/html
mv /vendor /var/www/html
mv /result.txt /var/www/html/result.php

mkdir /var/www/uploads
chmod 777 /var/www/uploads

sudo restart apache2
sudo apache2ctl graceful

sudo wget ec2-54-191-137-35.us-west-2.compute.amazonaws.com/fstab
sudo mv fstab /etc/
sudo mount -a
