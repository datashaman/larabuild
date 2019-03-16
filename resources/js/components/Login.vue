<template>
    <layout pageTitle="Login">
        <b-form @submit.prevent="onSubmit" @reset.prevent="onReset" v-if="show">
            <b-form-group
                label="Email Address"
                label-for="email"
                :invalid-feedback="errors.email && errors.email[0]"
                :state="form.email && errors.email"
            >
                <b-form-input
                    id="email"
                    type="email"
                    autocomplete="username"
                    :state="form.email && !errors.email"
                    v-model="form.email" />
            </b-form-group>

            <b-form-group
                label="Password"
                label-for="password"
                :invalid-feedback="errors.password && errors.password[0]"
                :state="form.password && !errors.password"
            >
                <b-form-input
                    id="password"
                    type="password"
                    autocomplete="current-password"
                    :state="form.password && !errors.password"
                    v-model="form.password" />
            </b-form-group>

            <b-button type="submit" variant="primary">Login</b-button>
            <b-button type="reset" variant="secondary">Reset</b-button>
        </b-form>
    </layout>
</template>

<script>
export default {
    data() {
        return {
            form: {
                email: '',
                password: '',
            },
            errors: {
                email: null,
                password: null,
            },
            show: true,
        }
    },
    methods: {
        onSubmit(evt) {
            if (this.form.email && this.form.password) {
                axios({
                    method: 'post',
                    url: '/login',
                    data: this.form,
                    maxRedirects: 0
                }).then(function (response) {
                    this.$emit('authenticated', response.data)
                }.bind(this)).catch(function (err) {
                    console.error('error', err)
                    this.errors = err.response.data.errors
                }.bind(this))
            }
        },
        onReset(evt) {
            this.form = {
                email: '',
                password: '',
            }
            this.errors = {
                email: null,
                password: null,
            }
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        },
    },
}
</script>
