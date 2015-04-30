sudo apt-get install libjpeg-turbo-progs -y
sudo apt-get install pngcrush -y
sudo apt-get install gifsicle -y
sudo apt-get install imagemagick -y

wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
phpunit --version