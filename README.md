# Hotel Manager

[![Build Status](https://travis-ci.org/Saibamen/HotelManager.svg)](https://travis-ci.org/Saibamen/HotelManager)
[![CircleCI](https://circleci.com/gh/Saibamen/HotelManager.svg?style=shield)](https://circleci.com/gh/Saibamen/HotelManager)
[![Codeship Status for Saibamen/HotelManager](https://app.codeship.com/projects/4b76fb80-a887-0135-d285-4ac701b81e22/status)](https://app.codeship.com/projects/256229)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Saibamen/HotelManager/badges/quality-score.png)](https://scrutinizer-ci.com/g/Saibamen/HotelManager/)
[![Maintainability](https://api.codeclimate.com/v1/badges/67e663aaa3bc230a2888/maintainability)](https://codeclimate.com/github/Saibamen/HotelManager/maintainability)
[![StyleCI](https://styleci.io/repos/77186372/shield)](https://styleci.io/repos/77186372)
[![SymfonyInsight](https://insight.symfony.com/projects/37d84994-c778-4373-94f2-a3218c22f96d/mini.svg)](https://insight.symfony.com/projects/37d84994-c778-4373-94f2-a3218c22f96d)
[![codecov](https://codecov.io/gh/Saibamen/HotelManager/branch/master/graph/badge.svg)](https://codecov.io/gh/Saibamen/HotelManager)

Web application for managing hotel rooms, guests and reservations with flexible and responsive frontend written in [Laravel](https://laravel.com) 5.6

![All reservations](github_images/all_reservations.PNG)

## Requirements

* [PHP](http://php.net) >= 7.1.3
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* [Composer](https://getcomposer.org)

## Installation

First, change `.env.example` to `.env` and update it

```
composer install --no-interaction
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Run by `php artisan serve`
