# LaraBuild

CI / CD server running on Laravel with a GraphQL API. Built for [LaraHack #3 2018](https://larahack.com).

Table of Contents
=================

   * [LaraBuild](#larabuild)
      * [Installation](#installation)
      * [Workflow](#workflow)
         * [Prepare project repository](#prepare-project-repository)
         * [Create entities](#create-entities)
            * [Create a team](#create-a-team)
            * [Create a project](#create-a-project)
            * [Build a project](#build-a-project)
      * [Screenshots](#screenshots)
      * [Models](#models)
      * [TODO](#todo)

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

Either `id` or `email` must be specified. `tokenName` should represent the GraphQL client, so _playground_ will do.

Assuming you use valet, open the [GraphQL playground](http://larabuild.test/graphql-playground).

Edit the _HTTP HEADERS_ and add an _Authorization_ header:

    {
        "Authorization": "Bearer token"
    }

If you use a client other than playground, you might have to add another header:

    {
        "X-Requested-With": "XMLHttpRequest"
    }

Replace the word `token` with the output from the access token command above.

Congratulations, you're connected to the GraphQL API!

## Workflow

### Prepare project repository

Create a private key for the deploy user:

    ssh-keygen

When prompted save the file locally in your folder somwewhere. Assuming you called it _larabuild_, it will generate _larabuild_ and _larabuild.pub_.

Create a new _deploy key_ (under Settings / Deploy Keys on _GitHub_) in the project repository, and paste the contents of _larabuild.pub_ into the form.

Add, commit and push a file to the project repository named `.larabuild.yml` containing the following content:

    install:
        - echo "hello world"

### Create entities

Example queries are shown as pseudo-JSON. Put the query and variables wherever they should go in your GraphQL client.

#### Create a team

Create a team to hold our project:

    {
        "query": "
            mutation createTeam($team: TeamInput!) {
                createTeam(team: $team) {
                    id
                    name
                }
            }
        ",
        "variables": {
            "name": "Example Team"
        }
    }

The JSON response will include the team's `id`:

    {
        "data": {
            "createTeam": {
                "id": 1,
                "name": "Example Team"
            }
        }
    }

#### Create a project

Use the `id` from above in `team_id` and the content of the private key file _larabuild_ for `private_key` below to create a project:

    {
        "query": "
            mutation createProject($project: CreateProjectInput!) {
                createProject(project: $project) {
                    id
                    team {
                        id
                        name
                    }
                    name
                    repository
                }
            }
        ",
        "variables": {
            "team_id": team_id,
            "name": "Example Project",
            "repository": "https://github.com/user/repository.git",
            "private_key": private_key
        }
    }

This will return a JSON response including the project `id`:

    {
        "data": {
            "createProject": {
                "id": 1,
                "team": {
                    "id": 1,
                    "name": "Example Team"
                },
                "name": "Example Project",
                "repository": "https://github.com/user/repository.git"
            }
        }
    }

#### Build a project

We will now generate our first build.  As an admin user, you can build projects in any team. Use the `project_id` from above:

    {
        "query": "
            mutation buildProject($id: ID!, $commit: String!) {
                buildProject(id: $id, commit: $commit) {
                    id
                    status
                    output
                }
            }
        ",
        "variables": {
            "id": project_id,
            "commit": "master"
        }
    }

The commands in the `install` value from `.larabuild.yml` file in the project repository will run and the build output and status will be shown as a JSON response:

    {
        "data": {
            "buildProject": {
                "id": "QnVpbGQ6NA==",
                "status": "success",
                "output": "hello world\n"
            }
        }
    }

If any of the commands listed fails during the process, the build has a status of `failed`.

If no `.larabuild.yml` file is found, the build has a status of `not-found`. While the job is running, the status is `started`.

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

## TODO

- Create an HTML interface for easier frontend management
- Add more features to `.larabuild.yml` format, such as service selection
- Run commands within a secure chroot jail or even docker
- Add timeout detection for the commands
- Streaming interface for the output
- Add webhook handler to automate the build process
