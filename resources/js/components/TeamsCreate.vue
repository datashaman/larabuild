<template>
    <b-form @submit.prevent="onSubmit" @reset.prevent="onReset" v-if="show">
        <b-form-group
            label="Name"
            label-for="name"
        >
            <b-form-input
                id="name"
                type="text"
                v-model="form.name"
                required />
        </b-form-group>

        <b-button type="submit" variant="primary">Submit</b-button>
        <b-button type="reset" variant="danger">Reset</b-button>
    </b-form>
</template>

<script>
import { TEAMS_QUERY, CREATE_TEAM_MUTATION } from '../graphql'
import { PER_PAGE } from '../constants'

export default {
    data() {
        return {
            form: {
                name: '',
            },
            show: true,
        }
    },
    methods: {
        onSubmit(evt) {
            this.$apollo.mutate({
                mutation: CREATE_TEAM_MUTATION,
                variables: {
                    team: this.form,
                },
                refetchQueries: [{
                    query: TEAMS_QUERY,
                    variables: {
                        count: PER_PAGE,
                    },
                }],
            }).then((data) => {
                this.form.name = ''
                this.$emit('success', data)
            }).catch((err) => {
                console.error(err)
            })
        },
        onReset(evt) {
            this.form.name = ''
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        },
    },
}
</script>

<style scoped>
input {
    max-width: 500px;
}
</style>
