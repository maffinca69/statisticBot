# Nutnet statistic bot

#### Used technologies and services:
- Lumen (core bot)
- Redis (refresh google account token, cache, queue)
- Laravel queue (with redis)
- Laravel schedules (send statistics every day)
- Cron (send statistics with helped laravel schedules)

#### Install
1. Git clone
2. ```cp .env.example .env```
2. ```cd docker```
2. ```cp .env.example .env```
2. ```docker-compose up -d```
2. ```docker exec -it statistic_fpm_1 /bin/sh```
2. ```composer install```

#### Refresh token automatically
Run after expired token:

```php artisan queue:work```

Also, app has secret url for refresh token (_routes/web.php_)

## Planned...
1. Test, test, test...
2. Simplify google authorization (by url which sent bot) 
