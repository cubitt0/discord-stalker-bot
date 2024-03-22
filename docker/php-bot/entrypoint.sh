#!/bin/bash

composer install -n

php bin/console doc:mig:mig --no-interaction
php bin/console doc:fix:load --no-interaction

php bin/console app:discord:run

exec "$@"