<template>
    <layout :pageTitle="team.name" :breadcrumb="breadcrumb">
        <b-tabs card v-model="tabIndex">
            <b-tab title="Projects">
                <b-tabs pills card vertical v-model="projects.tabIndex">
                    <b-tab title="Index">
                        <b-table
                            :fields="projects.fields"
                            head-variant="light"
                            hover
                            :items="team.projects"
                            no-local-sorting
                            primary-key="id"
                            @row-clicked="projectRowClicked"
                            small
                            :sort-by.sync="projects.sortBy"
                            @sort-changed="projectSortChanged"
                            :sort-desc.sync="projects.sortDesc"
                            v-if="team.projects">
                        </b-table>
                        <p v-else>No projects.</p>
                    </b-tab>
                    <b-tab title="Create">
                        <projects-create :team="id" @success="projectCreated" />
                    </b-tab>
                </b-tabs>
            </b-tab>
            <b-tab title="Users">
                <b-tabs pills card vertical v-model="users.tabIndex">
                    <b-tab title="Index">
                        <b-table
                            :fields="users.fields"
                            head-variant="light"
                            hover
                            :items="team.users"
                            no-local-sorting
                            primary-key="id"
                            @row-clicked="userRowClicked"
                            small
                            :sort-by.sync="users.sortBy"
                            @sort-changed="userSortChanged"
                            :sort-desc.sync="users.sortDesc"
                            v-if="team.users">
                            <template slot="userRoles" slot-scope="data">
                                {{ data.value.map((r) => r.role ) }}
                            </template>
                        </b-table>
                        <p v-else>No users.</p>
                    </b-tab>
                    <b-tab title="Create">
                        <users-create :team="id" @success="userCreated" />
                    </b-tab>
                </b-tabs>
            </b-tab>
        </b-tabs>
    </layout>
</template>

<script>
import { TEAM_QUERY } from '../graphql'

export default {
    props: [
        'id',
    ],
    data() {
        return {
            projects: {
                fields: [
                    {
                        key: 'name',
                        sortable: true,
                    },
                ],
                sortBy: 'name',
                sortDesc: false,
                tabIndex: 0,
            },
            tabIndex: 0,
            team: {},
            users: {
                fields: [
                    {
                        key: 'name',
                        sortable: true,
                    },
                    {
                        key: 'email',
                        sortable: true,
                    },
                    {
                        key: 'userRoles',
                    },
                ],
                sortBy: 'name',
                sortDesc: false,
                tabIndex: 0,
            },
        }
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
                    text: this.team.name,
                    to: {
                        name: 'teams.show',
                        params: {
                            id: this.id,
                        },
                    },
                    active: true,
                },
            ]
        },
    },
    apollo: {
        team: {
            query: TEAM_QUERY,
            variables() {
                return {
                    id: this.id,
                }
            },
        },
    },
    methods: {
        projectCreated(project) {
            this.projects.tabIndex = 0
        },
        projectRowClicked(project) {
            this.$router.push({
                name: 'projects.show',
                params: {
                    team: this.id,
                    id: project.id,
                },
            })
        },
        projectSortChanged(ctx) {
        },
        userCreated(project) {
            this.users.tabIndex = 0
        },
        userRowClicked(user) {
        },
        userSortChanged(ctx) {
        },
    },
}
</script>
