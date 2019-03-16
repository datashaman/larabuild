<template>
    <layout :pageTitle="team.name">
        <b-tabs card v-model="tabIndex">
            <b-tab title="Details">
            </b-tab>
            <b-tab title="Teams">
            </b-tab>
            <b-tab title="Builds">
            </b-tab>
        </b-tabs>
    </layout>
</template>

<script>
import { USER_QUERY } from '../graphql'

export default {
    props: [
        'id',
    ],
    data() {
        return {
            user: {},
        }
    },
    apollo: {
        user: {
            query: USER_QUERY,
            variables() {
                return {
                    id: this.id,
                }
            },
        },
    },
    methods: {
    },
}
</script>
