# Thinkphp Repositories

[![Latest Stable Version](http://poser.pugx.org/lingyun/think-repositories/v)](https://packagist.org/packages/lingyun/think-repositories) [![Total Downloads](http://poser.pugx.org/lingyun/think-repositories/downloads)](https://packagist.org/packages/lingyun/think-repositories) [![Latest Unstable Version](http://poser.pugx.org/lingyun/think-repositories/v/unstable)](https://packagist.org/packages/lingyun/think-repositories) [![License](http://poser.pugx.org/lingyun/think-repositories/license)](https://packagist.org/packages/lingyun/think-repositories) [![PHP Version Require](http://poser.pugx.org/lingyun/think-repositories/require/php)](https://packagist.org/packages/lingyun/think-repositories)

think-repositories is a package for Thinkphp 6.0 which is used to abstract the database layer. This makes applications much easier to maintain.

## Installation

Run the following command from you terminal:

```bash
composer require think/repositories
```

## Usage

First, create your repository class. Note that your repository class MUST extend `think\Repository` and implement model() method

```php
<?php
namespace app\repositories;

use think\Repository;

class FilmsRepository extends Repository {

    public function model() {
        return 'App\Film';
    }
}
```

It can be created using the `make:R` command:

`php think make:R admin@data/City common@data/DataCity --E=common@BaseRepository`

By implementing `model()` method you telling repository what model class you want to use. Now, create `App\Film` model:

```php
<?php
namespace app\model;

use think\Model;

class Film extends Model {

    protected $primaryKey = 'film_id';

    protected $table = 'film';

    protected $casts = [
        "rental_rate"       => 'float'
    ];
}
```

And finally, use the repository in the controller:

```php
<?php
namespace app\controller;

use app\repositories\FilmsRepository as Film;

class FilmsController extends Controller {

    private $film;

    public function __construct(Film $film) {

        $this->film = $film;
    }

    public function index() {
        return \Response::json($this->film->all());
    }
}
```

## Thanks

This package is largely inspired by [this](https://github.com/bosnadev/repository) great package by @bosnadev.
