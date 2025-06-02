#!/bin/sh
set -e
# Install Composer dependencies
composer install --no-interaction --optimize-autoloader --ignore-platform-reqs
