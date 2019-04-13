# Gastfreund - BackEnd Assessment
## Kanban Microservice

### Installation & Setup

1 - Clone the repository on your local machine.

```shell
$ git clone https://gitlab.com/branko.m.antic/kanban-microservice.git
```

2 - Create an empty database.
 
3 - Copy and configure .env (DB config, APP_KEY & WHITELISTED_IPS):

```shell
$ cd /kanban-microservice
$ cp .env.example .env
$ // set .env configuration
```

4 - Run dependency manager & run migration:

```shell
$ composer install
$ php artisan migrate --seed
```

5 - For phpunit tests run:

```shell
$ php vendor/bin/phpunit --configuration phpunit.xml
```

6 - Postman's collection configuration is exported to file `kanban_microservice.postman_collection.json`.
