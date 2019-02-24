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

## Workflow

Example queries are shown as JSON. Put the query and variables wherever they should go in your GraphQL client.

Create a team.

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

Create a private key for the deploy user:

    ssh-keygen

When prompted save the file locally in your folder somwewhere. Assuming you called it _larabuild_, it will generate _larabuild_ and _larabuild.pub_. Create a new _Deploy Key_ in the target repository, and paste the contents of _larabuild.pub_ into the form.

Add, commit and push a file in the project repository named `.larabuild.yml` containing the following content:

    install:
        - echo "hello world"

Now onto creating a project in that team.

Use the `id` from above in `team_id` and the content of the private key file _larabuild_ for `private_key` below to create a project.

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

This will return a JSON response including the project `id`. We will now generate our first build.  As an admin user, you can build projects in any team:


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
