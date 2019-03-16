<template>
    <layout :pageTitle="'Build #' + build.number" :breadcrumb="breadcrumb">
        Content Here
    </layout>
</template>

<script>
import { BUILD_QUERY } from '../graphql'

export default {
    props: [
        'project',
        'number',
    ],
    data() {
        return {
            build: {
                number: '',
                project: {
                    id: 'project',
                    team: {
                        id: 'team',
                    },
                },
            },
        }
    },
    apollo: {
        build: {
            query: BUILD_QUERY,
            variables() {
                return {
                    projectId: this.project,
                    number: this.number,
                }
            },
        },
    },
    computed: {
        breadcrumb() {
            return [
                {
                    text: 'Teams',
                    to: {
                        name: 'teams.index',
                    },
                },
                {
                    text: this.build.project.team.name,
                    to: {
                        name: 'teams.show',
                        params: {
                            id: this.build.project.team.id,
                        },
                    },
                },
                {
                    text: this.build.project.name,
                    to: {
                        name: 'projects.show',
                        params: {
                            id: this.build.project.id,
                        },
                    },
                },
                {
                    text: '#' + this.number,
                    to: {
                        name: 'builds.show',
                        params: {
                            team: this.build.project.team,
                            project: this.build.project.id,
                            number: this.number,
                        },
                    },
                    active: true,
                },
            ]
        },
    },
    methods: {
    },
}
</script>
