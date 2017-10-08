# Laravel App Bootstrap for Laravel 5.4+

This is a simple bootstrapping package for Laravel 5.4+ (it may work on 5.3 but it hasn't been tested) designed around my 
personal preferences, so it might not suit everyone, but you are welcome to fork it and adjust it to your needs.

Use this package exclusively on a fresh Laravel installation, as it will rewrite config options in `app.php` and `database.php`.

## What it does:

- pulls in [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper) and [Cascading Config](https://github.com/phanan/cascading-config)
packages
- generates IDE helper file and `localdev` environment files (`/config.localdev/...`)
- replaces `env()` calls in `/config/app.php` and `/config/database.php` with strings (the latter being fully overwritten)
- adds `/bootstrap/env.php` file and deletes `.env` file, effectively disposing of `DotEnv` usage in your application.   
You should [not use DotEnv in production](https://github.com/laravel/framework/issues/8191). Personally, I prefer not to
use it at all, and much prefer cascading config over it. Thank you [@Phan An](https://github.com/phanan/cascading-config)
- prompts user for application info and sets appropriate config options, and creates the database

## Installation

Use exclusively on a fresh Laravel installation.

- add `"gtcrais/laravel-app-bootstrap": "1.3.*"` to your `composer.json` and run `composer update`
- for Laravel 5.4 add `GTCrais\LaravelAppBootstrap\LaravelAppBootstrapServiceProvider::class,` to providers array in `/config/app.php`
- run `php artisan lab:setup`
- fill in data about your application

Your application is now set up, and using `localdev` environment.

## Notes

- Laravel App Bootstrap assumes your're using `MySQL` on `localhost`, port `3306`. If this is not the case, after bootstrapping 
you'll need to adjust your database config options and manually create the database.
- since getting rid of `.env` file obviously breaks `php artisan key:generate` command, Laravel App Bootstrap
provides a substitution: `lab:key-generate`

## License

Laravel App Bootstrap is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
