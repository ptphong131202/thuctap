<template>
    <span>
        <ul class="pagination pagination-sm no-margin pull-right"
                role="navigation"
                >
            <li v-for="page in paginate.links"
                    :key="page.label"
                    v-bind:class="{ disabled: page.url === null, active: page.active }"
                    aria-disabled="true">
                <a :href="page.active ? 'javascript:void(0);' : page.url" rel="next" :aria-label="page.label"><span v-html="page.label"></span></a>
            </li>
        </ul>
        <b class="pull-right" style="margin-right: 10px; margin-top: 7px;">
            {{ ((paginate.current_page - 1) * paginate.per_page) + 1 }} - {{ paginate.to }} / Tổng {{ paginate.total }}
        </b>
    </span>
</template>

<script>
    export default {
        props: {
            paginate: Object,
        }
    }
</script>
