dummy:

refresh:
	php artisan migrate:refresh --seed
	php artisan passport:client --personal --name "Laravel Personal Access Client"
	php artisan larabuild:access-token test --email admin-user@example.com
