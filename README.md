Product API
===========
Simple product API 

Requirements
------------
  * PHP 5.5 or above
  * MySQL 5.5 or above
  * Composer 1.2-dev or above

Instalation
-----------

``` 
$ composer install
$ bin/console doctrine:database:create
$ bin/console doctrine:schema:create
$ bin/console server:start
```

Usage
-----

* Create some articles: 
```
 $ curl -X POST http://127.0.0.1:8000/v1/articles -d article[title]=Orange -d article[body]="Sweet apple" -d article[tags][][name]=fruit
 $ curl -X POST http://127.0.0.1:8000/v1/articles -d article[title]=Carrot -d article[body]="Sweet carrot" -d article[tags][][name]=fruit
 $ curl -X POST http://127.0.0.1:8000/v1/articles -d article[title]=Olives -d article[body]="Virgin olives" -d article[tags][][name]=oil
```

* Get list of all articles:
```
 $ curl -X GET http://127.0.0.1:8000/v1/articles
```

* Get single article data:
```
 $ curl -X GET http://127.0.0.1:8000/v1/articles/{id}
```

* Update single article:
```
 $ curl -X PUT http://127.0.0.1:8000/v1/articles/{id} -d article[title]="Virgin olives" -d article[body]="Sweet olives" -d article[tags][][name]=oil -d article[tags][][name]=fruit
```

* Delete article:
```
 $ curl -X DELETE http://127.0.0.1:8000/v1/articles/{id}
```

Tests
-----
```
 $ bin/phpunit
```
