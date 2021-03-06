# API assessment 
## _at Online Republic_

This code is built with Laravel 8.x  and using Passport & Sail,a Docker development environment
Task: implement User API with authentication 
Postman collection provided at https://github.com/justintnz/test2108/blob/main/Online%20Republic.postman_collection.json

## To run this on local
- clone the code
- install Docker engine ( if you dont have one)
- run composer install to download all require packages

## Let's get dev env. ready with Docker, 
move to the cloned folder and run
```sh
$ php artisan sail:install
$ ./vendor/bin/sail up -d
$ ./vendor/bin/sail artisan key:generate
$ ./vendor/bin/sail artisan migrate
$ ./vendor/bin/sail artisan passport:install
$ ./vendor/bin/sail artisan db:seed
```
NOTE:
 1. artisan should be run as "./vendor/bin/sail artisan" so that laravel container can communicate with mysql container.
 2. All test users are created with password "password"
 3. Admin email is admin@admin.com

## Features
- REGISTER A USER:  [POST] {{DOMAIN}}/api/register
- USER LOGIN: [POST] {{DOMAIN}}/api/login ( return access_token)
- GET a USER:  [GET] {{DOMAIN}}/api/user/{{id}}
- GET USERs with paging (optional): [GET] {{DOMAIN}}/api/user?page={{p}} (p is positive integer)
- CREATE NEW USER:  [POST] {{DOMAIN}}/api/user  
  - required fields  'first_name','last_name','email' (unique),'phone', 'password'
  - new admin can only be created by Admin user ( with field 'is_admin' set to 1 )
- UPDATE USER:  [PUT] {{DOMAIN}}/api/user/{{id}} 
  - optional fields  'first_name','last_name','email' (unique),'phone', 'password'
  - password can only be updated by Admin user
- DELETE USER:  [DELETE] {{DOMAIN}}/api/user/{{id}}
  - only Admin can delete user

## TESTING 
``` sh
$ ./vendor/bin/sail artisan test --testsuite=Feature --stop-on-failure
```

