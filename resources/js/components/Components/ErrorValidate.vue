<template>
    <div class="text-danger errors" v-if="errors[field] != null"><i class="fa fa-warning"></i>{{ errors[field][0] }}</div>
</template>

<script>
    export default {
        props: {
            errors: {},
            field: '',
        }
    }
</script>
