<template>
    <b-form @submit.prevent="onSubmit" @reset.prevent="onReset" v-if="show">
        <b-form-group
            label="Commit"
            label-for="commit"
        >
            <b-form-input
                id="commit"
                type="text"
                v-model="form.commit"
                required />
        </b-form-group>

        <b-button type="submit" variant="primary">Submit</b-button>
    </b-form>
</template>

<script>
import { BUILD_PROJECT_MUTATION, PROJECT_QUERY } from '../graphql'

export default {
    props: [
        'project',
    ],
    data() {
        return {
            form: {
                commit: 'master',
            },
            show: true,
        }
    },
    methods: {
        onSubmit(evt) {
            this.$apollo.mutate({
                mutation: BUILD_PROJECT_MUTATION,
                variables: {
                    id: this.project,
                    commit: this.form.commit,
                },
                refetchQueries: [{
                    query: PROJECT_QUERY,
                    variables: {
                        id: this.project,
                    },
                }],
            }).then((response) => {
                this.form.commit = ''
                this.$emit('success', response.data.buildProject)
            }).catch((err) => {
                console.error(err)
            })
        },
        onReset(evt) {
            this.form.commit = ''
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        },
    },
}
</script>
