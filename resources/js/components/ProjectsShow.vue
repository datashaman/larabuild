<template>
    <layout :pageTitle="project.name" :breadcrumb="breadcrumb">
        <b-tabs card v-model="tabIndex">
            <b-tab title="Builds">
                <b-tabs pills card vertical v-model="builds.tabIndex">
                    <b-tab title="Index">
                        <b-table
                            :fields="builds.fields"
                            head-variant="light"
                            hover
                            :items="project.builds"
                            no-local-sorting
                            primary-key="id"
                            @row-clicked="buildRowClicked"
                            small
                            :sort-by.sync="builds.sortBy"
                            @sort-changed="buildSortChanged"
                            :sort-desc.sync="builds.sortDesc"
                            v-if="project.builds">
                            <template slot="createdAt" slot-scope="data">
                                {{ data.value ? (new Date(data.value)).toLocaleString() : '' }}
                            </template>
                            <template slot="console" slot-scope="data">
                                <b-link v-if="data.item.status !== 'new'" :href="'/' + project.team.id + '/' + project.id + '/' + data.item.number + '/console'">console</b-link>
                            </template>
                            <template slot="duration" slot-scope="data">
                                {{ data.item.completedAt ? (parseInt((new Date(data.item.completedAt) - new Date(data.item.createdAt))/1000)) : '' }}
                            </template>
                        </b-table>
                        <p v-else>No builds.</p>
                    </b-tab>
                    <b-tab title="Create">
                        <builds-create :project="id" @success="buildCreated" />
                    </b-tab>
                </b-tabs>
            </b-tab>
        </b-tabs>
    </layout>
</template>

<script>
import { PROJECT_QUERY } from '../graphql'

export default {
    props: [
        'id',
        'team',
    ],
    data() {
        return {
            builds: {
                fields: [
                    {
                        key: 'number',
                        sortable: true,
                    },
                    {
                        key: 'commit',
                        sortable: true,
                    },
                    {
                        key: 'status',
                        sortable: true,
                    },
                    {
                        key: 'createdAt',
                        sortable: true,
                    },
                    {
                        key: 'duration',
                    },
                    {
                        key: 'console',
                    },
                ]
            },
            project: {
                id: 'project',
                team: {
                    id: 'team',
                },
            },
            tabIndex: 0,
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
                    text: this.project.team.name,
                    to: {
                        name: 'teams.show',
                        params: {
                            id: this.project.team.id,
                        },
                    },
                },
                {
                    text: this.project.name,
                    to: {
                        name: 'projects.show',
                        params: {
                            id: this.project.id,
                        },
                    },
                    active: true,
                },
            ]
        },
    },
    apollo: {
        project: {
            query: PROJECT_QUERY,
            variables() {
                return {
                    id: this.id,
                }
            },
        },
    },
    mounted() {
        window.Echo
            .private('subscriptions')
            .listen('BuildUpdated', function () {
                this.$apollo.queries.project.refetch()
            }.bind(this))
    },
    methods: {
        buildCreated(build) {
            this.builds.tabIndex = 0
        },
        buildRowClicked(build) {
            this.$router.push({
                name: 'builds.show',
                params: {
                    team: this.project.team.id,
                    project: this.id,
                    number: build.number,
                },
            })
        },
        buildSortChanged() {
        },
    },
}
</script>
