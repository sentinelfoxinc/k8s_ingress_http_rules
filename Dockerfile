FROM php:7.4-apache

ENV PHP_SERVICE_AS=products

COPY index.php /var/www/html/index.php

EXPOSE 80