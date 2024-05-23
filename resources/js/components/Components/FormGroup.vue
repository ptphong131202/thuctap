<template>
    <div class="form-group" v-bind:class="{ 'has-error': errors[field] != null }">
        <slot></slot>
        <span class="help-block" v-if="errors[field] != null">{{ errors[field][0] }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            errors: {},
            field: '',
        }
    }
</script>
