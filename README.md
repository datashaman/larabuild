# LaraBuild

CI / CD server running on Laravel with a GraphQL API. Built for [LaraHack #3 2018](https://larahack.com).

## Installation

Setup a MySQL database and user for access.

Clone repository:

    git clone https://github.com/datashaman/larabuild.git

Install the composer dependencies:

    composer install

Edit the .env to your requirements.

Migrate the database (optionally seed with demo data):

    php artisan migrate

Generate Passport keys:

    php artisan passport:keys

Assuming you use valet, open the [GraphQL playground](http://larabuild.test/graphql-playground) to start using the GraphQL API.

## Screenshots

![Create Team](screenshots/Screenshot-Create-Team.png)

![Build Project](screenshots/Screenshot-Build-Project.png)

## Models

- team
    - belongsToMany users
    - hasMany projects
- user
    - belongsTo team
    - hasMany builds
- project
    - belongsTo team
    - hasMany builds
- build
    - belongsTo project
    - belongsTo user
