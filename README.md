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

Create an admin user:

    php artisan larabuild:user name email password --roles=admin

Generate a Bearer access token:

    php artisan larabuild:access-token tokenName [--id=userId] [--email=userEmail]

Either _id_ or _email_ must be specified. _tokenName_ should represent the GraphQL client, so _playground_ will do.

Assuming you use valet, open the [GraphQL playground](http://larabuild.test/graphql-playground).

Edit the _HTTP HEADERS_ and add an _Authorization_ header:

    {
        "Authorization": "Bearer token"
    }

If you use a client other than the playground, you might have to add another header:

    {
        "X-Requested-With": "XMLHttpRequest"
    }

Replace the word _token_ with the output from the command above.

Congratulations, you're connected to the GraphQL API!

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
