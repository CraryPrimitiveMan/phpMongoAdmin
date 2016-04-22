#!/bin/bash
path=$(dirname $(pwd))
php -S localhost:8081 -t ${path}
