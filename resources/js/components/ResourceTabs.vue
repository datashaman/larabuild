<template>
    <b-tabs pills card vertical>
        <b-tab title="Index"></b-tab>
        <b-tab :title="'Create' + label"></b-tab>
    </b-tabs>
</template>

<script>
export default {
    props: [
        'label',
        'resource',
    ],
}
</script>
