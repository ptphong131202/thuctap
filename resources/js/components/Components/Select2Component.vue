<template>
    <select class="form-control select2" :id="name" :name="name" v-model="selectedValue"/>
</template>

<script>
    export default {
        props: ['options', 'value', 'name'],
        data() {
            return {
                selectedValue: this.value,
            }
        },
        mounted() {
            var vm = this;
            $(this.$el).select2({
                    data: vm.options,
                    width: '100%',
                    language: {
                        noResults: function(){
                            return "Không tìm thấy kết quả";
                        }
                    },
                    placeholder: 'Nhập ký tự để tìm kiếm',
                })
                .val(vm.selectedValue)
                .trigger('change')
                .on('select2:select', function (e) {
                    vm.selectedValue = e.params.data.id
                    vm.$emit('input', vm.selectedValue);
                    vm.$emit('change');
                });
        },
        watch: {
            value: function (param) {
                $(this.$el).val(param).trigger('change');
            },
            options: function(param) {
                $(this.$el).select2({
                    data: param,
                    width: '100%'
                })
                .val(this.selectedValue)
                .trigger('change');
            },
            destroyed: function () {
                $(this.$el).off().select2('destroy');
            }
        }
    }
</script>
