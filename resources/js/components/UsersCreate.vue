<template>
    <b-form @submit.prevent="onSubmit" @reset.prevent="onReset" v-if="show">
        <b-form-group
            label="Name"
            label-for="user-name"
        >
            <b-form-input
                id="user-name"
                type="text"
                v-model="form.name"
                required />
        </b-form-group>

        <b-form-group
            label="Email Address"
            label-for="user-email"
        >
            <b-form-input
                id="user-email"
                type="email"
                autocomplete="username email"
                v-model="form.email"
                required />
        </b-form-group>

        <b-form-group
            label="Password"
            label-for="user-password"
        >
            <b-form-input
                id="password"
                type="password"
                autocomplete="new-password"
                v-model="form.password"
                required />
        </b-form-group>

        <b-form-group
            label="Password Confirmation"
            label-for="password_confirmation"
        >
            <b-form-input
                id="password_confirmation"
                type="password"
                autocomplete="new-password"
                v-model="form.password_confirmation"
                required />
        </b-form-group>

        <b-button type="submit" variant="primary">Submit</b-button>
    </b-form>
</template>

<script>
import { CREATE_TEAM_USER_MUTATION, TEAM_QUERY } from '../graphql'

export default {
    props: [
        'team',
    ],
    data() {
        return {
            form: {
                name: 'bob',
                email: 'bob@example.com',
                password: 'secret',
                password_confirmation: 'secret',
            },
            show: true,
        }
    },
    methods: {
        onSubmit(evt) {
            if (this.form.password !== this.form.password_confirmation) {
                // TODO
                return
            }

            this.$apollo.mutate({
                mutation: CREATE_TEAM_USER_MUTATION,
                variables: {
                    teamId: this.team,
                    user: {
                        email: this.form.email,
                        name: this.form.name,
                        password: this.form.password,
                    },
                },
                refetchQueries: [{
                    query: TEAM_QUERY,
                    variables: {
                        id: this.team,
                    },
                }],
            }).then((response) => {
                this.form.name = ''
                this.form.email = ''
                this.form.password = ''
                this.form.password_confirmation = ''
                this.$emit('success', response.data.createUser)
            }).catch((err) => {
                console.error(err)
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
