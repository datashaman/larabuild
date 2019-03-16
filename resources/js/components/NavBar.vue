<template>
    <b-navbar toggleable="lg" type="dark" variant="info">
        <b-container>
            <b-navbar-brand href="#">NavBar</b-navbar-brand>

            <b-navbar-toggle target="nav-collapse" />

            <b-collapse is-nav id="nav-collapse">
                <b-navbar-nav>
                    <b-nav-item :to="{ name: 'home' }">Home</b-nav-item>
                    <b-nav-item v-if="authenticated" :to="{ name: 'teams.index' }">Teams</b-nav-item>
                </b-navbar-nav>

                <b-navbar-nav class="ml-auto">
                    <b-nav-item v-if="authenticated" @click="logout">Logout</b-nav-item>
                </b-navbar-nav>
            </b-collapse>
        </b-container>
    </b-navbar>
</template>

<script>
export default {
    props: [
        'authenticated',
    ],
    methods: {
        logout() {
            this.$emit('logout')
        }
    }
}
</script>
