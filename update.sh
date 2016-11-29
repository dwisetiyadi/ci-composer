#!/bin/bash

composer update

cd CodeIgniter
git remote update
git pull origin 3.1-stable