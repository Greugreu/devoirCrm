# Devoir CRM

Symfony 6 avec PHP 8.0

## Stack

**Front-end** : Twig

**Back-End** : Symfony 6.0

## Install

[Symfony](https://symfony.com/download)

```bash
  #Install components
  $ composer install
    
  #create database
  $ php bin/console database:create
  
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