# rift3
My own home automation interface (Raspberry Pi, ESP8266, 433MHz)

## Changelog ##
??.??.??: Initial commit v1
??.??.??: Update/Rewrite v2
17.10.06: added switches on homescreen + api to toggle switches
17.10.11: added widgets (no config until now), new icons, style-changes

## Installation ##
Based on rasbian stretch and **lighttpd** as webserver
```
sudo -s
apt-get update --fix-missing
apt-get -y install nano dnsutils fail2ban
apt-get -y install lighttpd php-common php-cgi php php-curl

groupadd www-data && usermod -G www-data -a pi
lighty-enable-mod fastcgi
lighty-enable-mod fastcgi-php
lighty-enable-mod rewrite

rm /var/www/html/index.lighttpd.html
echo "<?php phpinfo(); ?>">/var/www/html/index.php
cat /var/www/html/index.php

chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html

service lighttpd force-reload
systemctl restart lighttpd

mkdir /var/www/html/rift3
cd /var/www/html/rift3
wget --no-check-certificate https://github.com/ml17950/rift3/archive/master.tar.gz
tar -xvzf master.tar.gz --strip 1
rm master.tar.gz
chown -R www-data:www-data /var/www/html/rift3

echo "">>/etc/lighttpd/lighttpd.conf
echo "url.rewrite-if-not-file = (">>/etc/lighttpd/lighttpd.conf
echo "        \"^/rift3/api/([^?]*)?$\" => \"/rift3/api.php?path=$1&data=undefined\",">>/etc/lighttpd/lighttpd.conf
echo "        \"^/rift3/api/([^?]*)\?(.*)?$\" => \"/rift3/api.php?path=$1&data=$2\"">>/etc/lighttpd/lighttpd.conf
echo ")">>/etc/lighttpd/lighttpd.conf
cat /etc/lighttpd/lighttpd.conf
```