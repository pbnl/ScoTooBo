sleep 5

php bin/console doctrine:schema:update --force
export SYMFONY_ENV=prod
export APP_ENV=prod
composer run-script post-install-cmd --no-interaction --no-dev

rsync -rtvu /public-original/* /var/www/app/public

php-fpm