# LaraBuild

CI / CD server running on Laravel with a GraphQL API. Built for [LaraHack #3 2018](https://larahack.com).

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
