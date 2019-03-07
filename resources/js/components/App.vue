<template>
    <div>
        <nav-bar :authenticated="authenticated" @logout="logout"></nav-bar>

        <b-container>
            <router-view @authenticated="setAuthenticated"></router-view>
        </b-container>
    </div>
</template>

<script>
export default {
    data() {
        return {
            apiToken: null,
            authenticated: false,
        }
    },
    mounted() {
        axios.get('/auth/me')
            .then(function(response) {
                localStorage.setItem('apiToken', response.data.api_token)
                this.authenticated = true
            }.bind(this))
            .catch(function(err) {
                this.$router.replace({ name: 'login' })
            }.bind(this))
    },
    methods: {
        setAuthenticated(user) {
            localStorage.setItem('apiToken', user.api_token)
            this.authenticated = true
            this.$router.replace({ name: 'teams.index' })
        },
        logout() {
            axios.post('/logout')
                .then(function(response) {
                    localStorage.removeItem('apiToken')
                    this.authenticated = false
                    this.$router.replace({ name: 'login' })
                }.bind(this))
                .catch(function(err) {
                    console.error(err)
                })
        }
    }
}
</script>
