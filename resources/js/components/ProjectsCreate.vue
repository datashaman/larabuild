<template>
    <b-form @submit.prevent="onSubmit" @reset.prevent="onReset" v-if="show">
        <b-form-group
            label="ID"
            label-for="project-id"
        >
            <b-form-input
                id="project-id"
                type="text"
                v-model="form.id"
                required />
        </b-form-group>

        <b-form-group
            label="Name"
            label-for="project-name"
        >
            <b-form-input
                id="project-name"
                type="text"
                v-model="form.name"
                required />
        </b-form-group>

        <b-form-group
            label="Repository"
            label-for="project-repository"
        >
            <b-form-input
                id="project-repository"
                type="text"
                v-model="form.repository"
                required />
        </b-form-group>

        <b-form-group
            label="Private Key"
            label-for="project-privateKey"
        >
            <b-form-textarea
                id="project-privateKey"
                v-model="form.privateKey"
                rows="6"
                max-rows="10"
                required />
        </b-form-group>

        <b-button type="submit" variant="primary">Submit</b-button>
        <b-button type="reset" variant="danger">Reset</b-button>
    </b-form>
</template>

<script>
import { CREATE_PROJECT_MUTATION, TEAM_QUERY } from '../graphql'

export default {
    props: [
        'team',
    ],
    data() {
        return {
            form: {
                id: '',
                name: '',
                repository: '',
                privateKey: '',
            },
            show: true,
        }
    },
    methods: {
        onSubmit(evt) {
            let project = this.form
            project.teamId = this.team

            this.$apollo.mutate({
                mutation: CREATE_PROJECT_MUTATION,
                variables: {
                    project: project,
                },
                refetchQueries: [{
                    query: TEAM_QUERY,
                    variables: {
                        id: this.team,
                    },
                }],
            }).then((data) => {
                this.form.id = ''
                this.form.name = ''
                this.form.repository = ''
                this.form.privateKey = ''
                this.$emit('success', data)
            }).catch((err) => {
                console.error(err)
            })
        },
        onReset(evt) {
            this.form.id = ''
            this.form.name = ''
            this.form.repository = ''
            this.form.privateKey = ''
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        },
    },
}
</script>

<style scoped>
input, textarea {
    max-width: 500px;
}
</style>
