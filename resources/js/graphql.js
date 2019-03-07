import gql from 'graphql-tag'

export const BUILD_QUERY = gql`
    query Build($projectId: ID!, $number: Int!) {
        build(projectId: $projectId, number: $number) {
            id
            number
            commit
            status
            output
            createdAt
            completedAt
            project {
                id
                name
                team {
                    id
                    name
                }
            }
        }
    }
`

export const PROJECT_QUERY = gql`
    query Project($id: ID!) {
        project(id: $id) {
            id
            name
            team {
                id
                name
            }
            builds {
                id
                number
                commit
                status
                createdAt
                completedAt
            }
        }
    }
`

export const TEAM_QUERY = gql`
    query Team($id: ID!) {
        team(id: $id) {
            id
            name
            projectCount
            projects {
                id
                name
            }
            userCount
            users {
                id
                name
                email
                userRoles {
                    role
                    team {
                        id
                        name
                    }
                }
            }
        }
    }
`

export const TEAMS_QUERY = gql`
    query Teams($count: Int!, $page: Int, $sortBy: String, $sortDesc: Boolean) {
        teams(count: $count, page: $page, sortBy: $sortBy, sortDesc: $sortDesc) {
            paginatorInfo {
                lastPage
                total
            }
            data {
                id
                name
                projectCount
                userCount
            }
        }
    }
`

export const USER_QUERY = gql`
    query User($id: ID!) {
        user(id: $id) {
            id
            name
            email
        }
    }
`

export const BUILD_PROJECT_MUTATION = gql`
    mutation BuildProject($id: ID!, $commit: String!) {
        buildProject(id: $id, commit: $commit)
    }
`

export const CREATE_PROJECT_MUTATION = gql`
    mutation CreateProject($project: CreateProjectInput!) {
        createProject(project: $project) {
            id
            name
        }
    }
`

export const CREATE_TEAM_MUTATION = gql`
    mutation CreateTeam($team: TeamInput!) {
        createTeam(team: $team) {
            id
            name
        }
    }
`

export const CREATE_TEAM_USER_MUTATION = gql`
    mutation CreateTeamUser($teamId: ID!, $user: UserInput!) {
        createTeamUser(teamId: $teamId, user: $user) {
            id
            name
            email
        }
    }
`
export const CREATE_USER_MUTATION = gql`
    mutation CreateUser($user: UserInput!) {
        createUser(user: $user) {
            id
            name
            email
        }
    }
`
