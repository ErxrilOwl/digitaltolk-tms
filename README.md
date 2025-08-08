# DigitalTolk - Translation Management Service

> **Note:** This repository contains the coding test solution for the Translation Management Service, built with Laravel and Docker.

## Getting Started

### Install Application
Install [Docker](https://www.docker.com/get-started/) for your particular OS

### Building & Running Container
Once the Docker engine is running on the host machine, open a terminal in the projects' root directory and enter the following commands:

- `docker-compose build` - this builds the required Docker containers. It will take a while initially.
- `docker-compose up` - this starts all the development Docker containers.
- Copy the file from `build\env\app.env` to `html\.env`
- `docker-compose exec app php artisan key:generate`
- `docker-compose exec app php artisan migrate:fresh --seed` - This will populate the database with around 120,000 translations

### Design Explanation
File structure-wise, this separates the all the build configuration from the actual app, specially in the event when you don't want to run the Laravel app inside docker.

### Solution Explanation
- I created models for Language (for future proofing if we want to add new language in the future), Translation and Tags which has relations to each other.
- For the seeder, I added a default user with credentials of `admin@digitaltolk.com | p@ssW0rd`. This will be used for getting the auth token from `localhost/api/auth/login`.
- The token can be then used in API either manually or via the Swagger API in `localhost/api/documentation`
- The Translation API includes get (w/ search), get single, create, update, delete and export, which also then utilizes caching. 
- The unit tests can be found under `test/Feature` can be run via `docker-compose exec app php artisan test`
- As extra, I also implemented adding of Language.