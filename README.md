User Ratings for Laravel 5
==========================

This package allows you to attach user ratings an Eloquent model in [**Laravel 5**](http://laravel.com/).
The ratings include an integer field for a numeric rating; boolean fields for like, dislike and favorite and a text field for a comment.


Composer Install
----------------

It can be found on [Packagist](https://packagist.org/packages/craigzeaross/user-ratings).
The recommended way is through [composer](http://getcomposer.org).

Edit `composer.json` and add:

```json
{
    "require": {
        "craigzearfoss/user-ratings": "dev-master"
    }
}
```

And install dependencies:
```bash
$ composer update
```

If you do not have [**Composer**](https://getcomposer.org) installed, run these two commands:

```bash
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```


Install and then Run the migrations
-----------------------------------

Find the `providers` array key in `config/app.php` and register the **User Ratings Service Provider**.

```php
'providers' => array(
    // ...

    Craigzearfoss\UserRatings\UserRatingsServiceProvider::class,
)
```

Run the migration to create the `user_ratings` table.
```bash
php artisan vendor:publish --provider="Craigzearfoss\UserRatings\Providers\UserRatingsServiceProvider"
php artisan migrate
```


Configuration
-------------

In your model add the UserRatableTrait.

```php
<?php

// ...
use Craigzearfoss\UserRatings\UserRatableTrait;

class MyModel extends Model
{
    use UserRatableTrait;
```




Usage
-----
@TODO
 

Changelog
---------

[See the CHANGELOG file](https://github.com/craigzearfoss/user-ratings/blob/master/CHANGELOG.md)


Support
-------

[Please open an issue on GitHub](https://github.com/craigzearfoss/user-ratings/issues)


Contributor Code of Conduct
---------------------------

Please note that this project is released with a Contributor Code of Conduct.
By participating in this project you agree to abide by its terms.


License
-------

UserRatings is released under the MIT License. See the bundled
[LICENSE](https://github.com/craigzearfoss/user-ratings/blob/master/LICENSE)
file for details.