# Thinkphp Repositories


think-repositories is a package for Thinkphp 6.0 which is used to abstract the database layer. This makes applications much easier to maintain.

## Installation

Run the following command from you terminal:


 ```bash
 composer require "lingyun/repositories: 0.*"
 ```

or add this to require section in your composer.json file:

 ```
 "lingyun/repositories": "0.*"
 ```

then run ```composer update```


## Usage

First, create your repository class. Note that your repository class MUST extend ```lingyun\repositories\Repository``` and implement model() method

```php
<?php 
namespace app\repositories;

use lingyun\repositories\Contracts\RepositoryInterface;
use lingyun\repositories\Repository;

class FilmsRepository extends Repository {

    public function model() {
        return 'App\Film';
    }
}
```
It can be created using the ```make:R``` command:

```php think make:R admin@data/City common@data/DataCity --E=common@BaseRepository```


By implementing ```model()``` method you telling repository what model class you want to use. Now, create ```App\Film``` model:

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

## Available Methods

The following methods are available:

##### lingyun\repositories\Contracts\RepositoryInterface

```php
public function all($columns = array('*'))
public function lists($value, $key = null)
public function paginate($perPage = 1, $columns = array('*'));
public function create(array $data)
// if you use mongodb then you'll need to specify primary key $attribute
public function update(array $data, $id, $attribute = "id")
public function delete($id)
public function find($id, $columns = array('*'))
public function findBy($field, $value, $columns = array('*'))
public function findAllBy($field, $value, $columns = array('*'))
public function findWhere($where, $columns = array('*'))
```


### Example usage


Create a new film in repository:

```php
$this->film->create(Input::all());
```

Update existing film:

```php
$this->film->update(Input::all(), $film_id);
```

Delete film:

```php
$this->film->delete($id);
```

Find film by film_id;

```php
$this->film->find($id);
```

you can also chose what columns to fetch:

```php
$this->film->find($id, ['title', 'description', 'release_date']);
```

Get a single row by a single column criteria.

```php
$this->film->findBy('title', $title);
```

Or you can get all rows by a single column criteria.
```php
$this->film->findAllBy('author_id', $author_id);
```

Get all results by multiple fields

```php
$this->film->findWhere([
    'author_id' => $author_id,
    ['year','>',$year]
]);
```


## Thanks

This package is largely inspired by [this](https://github.com/bosnadev/repository) great package by @bosnadev. 
