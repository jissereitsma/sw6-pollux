#!/bin/bash

echo "CREATE DATABASE shopware" | mysql -u root --password=root
mysql -u root --password=root shopware < dump.sql

cp .env.gitpod .env

bin/console system:generate-jwt-secret
bin/console system:generate-app-secret