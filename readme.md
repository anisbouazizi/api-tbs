
# API TBS

## Run Locally

Clone the project

```bash
  git@github.com:anisbouazizi/api-tbs.git
```

Run the docker-compose

```bash
  docker-compose up -d
```

Log into the PHP container

```bash
  docker exec -it www_docker_api_bts
```

Inside the php container, run composer install 
```bash
 composer install
```
create the database :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```

automatically generate data :

```bash
php bin/console doctrine:fixtures:load
```

create the test database :

```bash
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:creat
```

automatically generate data of test :

```bash
php bin/console --env=test doctrine:fixtures:load
```


*Your application is available at http://localhost:8741/*

*Your application is available at http://localhost:8741/api/doc*


## Author

- [@yanisbouazizi](https://github.com/anisbouazizi)
