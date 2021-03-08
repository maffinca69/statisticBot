# Nutnet statistic bot

#### Used technologies and services:
- Lumen (core bot)
- Redis (refresh google account token, cache)
- Laravel queue (with redis)
- Laravel schedules (send statistics every day)
- Cron (send statistics with helped laravel schedules)
- Docker (local workspace)
- Google OAuth 2.0 (login google account)
- Supervisor (queue)

#### Install
1. Git clone
2. ```cp .env.example .env```
2. ```cd docker```
2. ```cp .env.example .env```
2. Setting bot token and google api keys
2. ```docker-compose up -d```
2. ```docker exec -it statistic_fpm_1 /bin/sh```
2. ```composer install```

#### Refresh token automatically
Run after authorized:

```
php artisan queue:work
```
#### Refresh token manually

```
php artisan token:refresh
php artisan queue:work
```


## Planned
1. Test, test, test...
