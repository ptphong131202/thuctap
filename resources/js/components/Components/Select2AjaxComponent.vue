<template>
    <select class="form-control select2"
            :id="name"
            :name="name"
            v-model="selectedValue"/>
</template>

<script>
    function formatRepoSelection (repo) {
        return repo.text || repo.text;
    }

    function formatRepo (repo) {
        if (repo.loading) {
            return 'Đang tìm kiếm';
        }

        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__avatar'></div>" +
            "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'></div>" +
                "</div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.text);

        return $container;
    }

    export default {
        props: ['list', 'value', 'name'],
        data() {
            return {
                selectedValue: this.value,
            }
        },
        mounted() {
            var vm = this;
            var el = $(this.$el);
            el.select2(this.createOptions())
                .on('select2:select', function (e) {
                    vm.selectedValue = e.params.data.id
                    vm.$emit('input', vm.selectedValue);
                    vm.$emit('change');
                });
            
            $.ajax({
                type: 'GET',
                url: this.list + '/' + this.selectedValue
            }).then(function (data) {
                // create the option and append to Select2
                var option = new Option(data.text, data.id, true, true);
                el.append(option).trigger('change');

                // manually trigger the `select2:select` event
                el.trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            });
        },
        watch: {
            value: function (param) {
                $(this.$el).val(param).trigger('change');
            },
            destroyed: function () {
                $(this.$el).off().select2('destroy');
            }
        },
        methods: {
            createOptions: function () {
                var vm = this;
                return {
                    ajax: {
                        url: vm.list,
                        dataType: 'json',
                        // delay: 100,
                        data: function (params) {
                            return {
                                search: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            // parse the results into the format expected by Select2
                            // since we are using custom formatting functions we do not need to
                            // alter the remote JSON data, except to indicate that infinite
                            // scrolling can be used
                            params.page = params.page || 1;
                            return {
                                results: data.items,
                                pagination: {
                                    more: (params.page * 10) < data.total_count
                                }
                            };
                        },
                        cache: true
                    },
                    language: {
                        noResults: function(){
                            return "Không tìm thấy kết quả";
                        }
                    },
                    placeholder: 'Nhập ký tự để tìm kiếm',
                    minimumInputLength: 0,
                    templateResult: formatRepo,
                    templateSelection: formatRepoSelection,
                    width: '100%',
                }
            }
        }
    }
</script>
