#!/bin/bash
php -r "copy('https://phar.phpunit.de/phpunit-6.3.phar', 'phpunit.phar');"
php phpunit.phar tests/*
php -r "unlink('phpunit.phar');"
if [ -z $1 ]
then
		read bye
fi