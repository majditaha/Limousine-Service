install:
	composer install
	php artisan key:generate
	php artisan migrate --force
	make assets
	make permissions

prepare:
	composer install
	php artisan migrate --force
	make assets

npm:
	npm install

assets: npm
	npm run prod

watch:
	npm run watch

permissions:
	chmod -R 775 ./bootstrap
	chmod -R 775 ./storage

logs:
	tail -n 100 -f storage/logs/laravel.log

lint:
	node ./node_modules/eslint/bin/eslint.js resources --ext js,vue

lintfix:
	node ./node_modules/eslint/bin/eslint.js resources --ext js,vue --fix
