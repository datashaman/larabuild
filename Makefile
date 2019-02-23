prepare-testing:
	php artisan migrate --env=testing
	APP_ENV=testing php artisan passport:install
	APP_ENV=testing php artisan passport:client --personal --name="Laravel Personal Access Client"
