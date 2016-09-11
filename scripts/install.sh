#!/bin/bash

set -xe
a2enmod rewrite

apt-get update
apt-get install -y libpng12-dev libjpeg-dev libmcrypt-dev

rm -rf /var/lib/apt/lists/*
docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr
docker-php-ext-install gd mcrypt mbstring mysqli zip

cd /var/www/html

export OPENCART_VER=2.2.0.0
export OPENCART_MD5=a9219f14b3483f867ea48218a0bf215d
export OPENCART_URL=https://github.com/opencart/opencart/archive/${OPENCART_VER}.tar.gz
export OPENCART_FILE=opencart.tar.gz

curl -sSL ${OPENCART_URL} -o ${OPENCART_FILE}
echo "${OPENCART_MD5}  ${OPENCART_FILE}" | md5sum -c
tar xzf ${OPENCART_FILE} --strip 2 --wildcards '*/upload/'
mv config-dist.php config.php
mv admin/config-dist.php admin/config.php
rm ${OPENCART_FILE}
chown -R www-data:www-data .

# setup ftp server for module uploads
apt install -y vsftpd libssh-dev
docker-php-ext-install ftp

grep -v "www-data" /etc/shadow > /etc/shadow.orig
cat /etc/shadow.orig > /etc/shadow
echo 'www-data:$6$MBUyqDrZ$2e9q7/86hAAc0CaN/4MuGq7ojX0PADsSPKpc09101ZDTao2R53VZ2e4rAhQefSP8LUHGKwLUIbydacJWrbvse0:16978:0:99999:7:::' >> /etc/shadow
chsh www-data -s /bin/bash
sed -i 's|#write_enable=YES|write_enable=YES|g' /etc/vsftpd.conf
/etc/init.d/vsftpd start


/etc/init.d/apache2 reload
