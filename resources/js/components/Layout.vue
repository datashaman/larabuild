<template>
    <div class="content">
        <b-breadcrumb v-if="breadcrumb" :items="breadcrumb"></b-breadcrumb>

        <h1 v-if="pageTitle">{{ pageTitle }}</h1>

        <slot></slot>
    </div>
</template>

<script>
export default {
    props: [
        'breadcrumb',
        'pageTitle',
    ],
}
</script>

<style scoped>
.content {
    padding: 10px 0;
}
</style>
