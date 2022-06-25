## LARATRACK

#### About
Laratrack is a Laravel based Error Tracking Tool for your projects. It's easy to install and use! ;)

#### Installation
- cp .env.example .env
- composer install
- php artisan key:generate
- npm install OR npm install --force --legacy-peer-deps
- npm run dev
- php artisan migrate --seed
- php artisan storage:link

#### Default login
- admin@admin.com / password

#### Getting Started
- Add sha1 based API keys into your .env file: 'LARATRACK_APIKEY_PROD' and 
```
LARATRACK_APIKEY_PROD= #prod key (90 days of log retation - 1h mail notifications for each error type)
LARATRACK_APIKEY_DEV= #prod key (3 days of log retation - no mail notifications)
```
- Remember to set a valid SMTP configuration, name and URL into your .env file
- Install the client into your project: composer require andreapollastri/laratrack
- Modify your client project:
```php
    // app/Exceptions/Handler.php
    public function report(Throwable $exception)
    {
        $log = New \Andr3a\Laratrack\Laratrack();
        $log->shouldReport($exception);

        parent::report($exception);
    }
```
- Add these vars into your client project .env:
```
LARATRACK_API_KEY=#TheSameKeyOfYourMainProject(Dev or Prod)
LARATRACK_ENDPOINT=#TheMainProjectUrl/data
```
