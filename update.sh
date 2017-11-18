#!/bin/bash

php composer.phar update

cd framework
git remote update
git pull origin 3.1-stable