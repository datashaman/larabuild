<template>
    <layout pageTitle="Teams" :breadcrumb="breadcrumb">
        <b-tabs pills card vertical v-model="tabIndex">
            <b-tab title="Index">
                <b-table
                    v-if="teams.data"
                    head-variant="light"
                    hover
                    no-local-sorting
                    small
                    :fields="fields"
                    :items="teams.data"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    @row-clicked="rowClicked"
                    @sort-changed="sortChanged">
                    <template slot="projectCount" slot-scope="data">
                        {{ data.value || '' }}
                    </template>
                    <template slot="userCount" slot-scope="data">
                        {{ data.value || '' }}
                    </template>
                </b-table>
                <b-pagination v-if="teams.data" align="center"
                    size="sm"
                    :per-page="count"
                    :total-rows="teams.paginatorInfo.total"
                    v-model="currentPage" />
                <p v-else>No teams.</p>
            </b-tab>
            <b-tab title="Create">
                <teams-create @success="teamCreated" />
            </b-tab>
        </b-tabs>
    </layout>
</template>

<script>
import { TEAMS_QUERY } from '../graphql'
import { PER_PAGE } from '../constants'

export default {
    apollo: {
        teams() {
            return {
                query: TEAMS_QUERY,
                variables() {
                    return {
                        count: PER_PAGE,
                        page: this.currentPage,
                        sortBy: this.sortBy,
                        sortDesc: this.sortDesc,
                    }
                },
            }
        },
    },
    data() {
        return {
            currentPage: 1,
            count: PER_PAGE,
            fields: [
                {
                    key: 'name',
                    sortable: true,
                },
                {
                    key: 'projectCount',
                    label: 'Projects',
                },
                {
                    key: 'userCount',
                    label: 'Users',
                }
            ],
            sortBy: this.sortBy,
            sortDesc: this.sortDesc,
            tabIndex: 0,
            teams: {
                paginatorInfo: {},
                data: [],
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
                    active: true,
                },
            ]
        },
    },
    methods: {
        rowClicked(item, index, event) {
            this.$router.push({
                name: 'teams.show',
                params: {
                    id: item.id,
                },
            })
        },
        sortChanged(ctx) {
            this.$apollo.queries.teams.refetch({
                count: this.count,
                page: 1,
                sortBy: ctx.sortBy,
                sortDesc: ctx.sortDesc,
            })
        },
        teamCreated(team) {
            this.tabIndex = 0
        },
    }
}
</script>
