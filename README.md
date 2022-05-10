# Devoir CRM

## Stack

**Front-end** : Twig

**Back-End** : Symfony 6.0 && PHP 8.0

## Install

[Symfony website](https://symfony.com/download)

```bash
  !! adapt .env file to your database configuration
  
  #Install components
  $ composer install
    
  #create database
  $ php bin/console doctrine:database:create
  
  #Load migrations
  $ php bin/console make:migrations
  $ php bin/console doctrine:migrations:migrate
  
  #load user fixtures
  $ php bin/console database:fixtures:load
  
  #build front end webpack
  $ npm run dev
  
  #start server  
  $ symfony server:start
  
  http://127.0.0.1:8000/home
```

## Author
- [Victor Clarke](https://github.com/Greugreu)