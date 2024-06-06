<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Niên khóa
                <button class="btn btn-danger">
                    <a href="javascript:void(0);"  style="color: white;"
                            title="Thêm mới"
                            v-on:click="create()">
                        <i class="fa fa-plus"></i> Thêm mới
                    </a>
                </button>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Niên khóa</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-body">
                            <table class="table table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="w-3 text-center">#</th>
                                        <th>Niên khóa</th>
                                        <th class="w-90-p text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(nk, index) in table.list.data" :key="nk.nk_id">
                                        <td class="text-center">{{ index + 1 + ((table.list.current_page - 1) * table.list.per_page) }}</td>
                                        <td>{{ nk.nk_ten }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn bg-orange btn-sm pull-left"
                                                    title="Thay đổi"
                                                    v-on:click="edit(nk.nk_id)"><i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm pull-right"
                                                    title="Xóa"
                                                    v-if="!nk.lop_hoc_exists"
                                                    v-on:click="destroy(nk.nk_id)"><i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="table.list.data == null || table.list.data.length == 0">
                                        <td colspan="100" class="text-center">{{ table.list.data != null ? 'Không tìm thấy dữ liệu' : 'Đang tải' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer">
                            <paginate :paginate="table.list" v-if="table.list != Object"></paginate>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->

        <div class="modal fade" id="nien-khoa-edit-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{{ editForm.reference == Object ? 'Thêm niên khóa' : 'Niên khóa ' + editForm.reference.nk_ten }}</h4>
                    </div>
                    <div class="modal-body">
                        <form-group :errors="editForm.errors" :field="'nk_ten'">
                            <label>Niên khóa</label>
                            <input type="text" class="form-control" v-model="editForm.model.nk_ten" />
                        </form-group>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> Hủy bỏ</button>
                        <button type="button" class="btn bg-purple" v-on:click="save"><i class="fa fa-save"></i> Lưu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const editModal = {
        modal: function () {
            return $('#nien-khoa-edit-modal');
        },
        show: function () {
            this.modal().modal({ keyboard: false, backdrop: 'static' });
        },
        hide: function () {
            this.modal().modal('hide');
        }
    }

    const consumer = {
        getListNienKhoa: function () {
            const url = 'http://localhost/cea-2.1/public/api/nien-khoa' + window.location.search;
            return axios.get(url).then(response => response.data);
        },
        getNienKhoa: function (nk_id) {
            const url = 'http://localhost/cea-2.1/public/api/nien-khoa/' + nk_id;
            return axios.get(url).then(response => response.data);
        },
        saveOrUpdate: function (formData) {
            if (formData.nk_id == null) {
                return axios.post('http://localhost/cea-2.1/public/api/nien-khoa', formData);
            } else {
                return axios.put('http://localhost/cea-2.1/public/api/nien-khoa/' + formData.nk_id, formData);
            }
        },
        destroy: function (nk_id) {
            return axios.delete('http://localhost/cea-2.1/public/api/nien-khoa/' + nk_id);
        }
    }

    const store = {
        table: {
            set list(data) {
                window.localStorage.setItem('nk.table.list', JSON.stringify(data));
            },
            get list() {
                return window.localStorage.getItem('nk.table.list') ? JSON.parse(window.localStorage.getItem('nk.table.list')) : {};
            }
        }
    }

    export default {
        mounted() {
            this.reloadList();
        },
        data() {
            return {
                editForm: {
                    reference: Object,
                    model: Object,
                    errors: Object,
                },
                table: {
                    list: store.table.list,
                }
            }
        },
        methods: {
            reloadList: function () {
                var vm = this;
                consumer.getListNienKhoa().then(data => {
                    Vue.set(vm.table, 'list', data);
                    store.table.list = data;
                });
            },
            resetEditForm: function () {
                this.editForm = {
                    reference: Object,
                    model: {},
                };
                Vue.set(this.editForm, 'errors', {});
            },
            create: function () {
                this.resetEditForm();
                editModal.show();
            },
            save: function () {
                if (this.editForm.model.nk_id == null) {
                    this.store();
                } else {
                    this.update();
                }
            },
            store: function () {
                var vm = this;
                consumer.saveOrUpdate(this.editForm.model)
                    .then(response => {
                        if (response.status == 200) {
                            vm.resetEditForm();
                            vm.reloadList();
                            editModal.hide();
                            AlertBox.Notify.Success('Thêm thành công');
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        }
                    });
            },
            edit: function (nk_id) {
                this.resetEditForm();
                consumer.getNienKhoa(nk_id).then(data => {
                    this.editForm.model = data;
                    this.editForm.reference = { ...this.editForm.model };
                    editModal.show();
                })
            },
            update: function () {
                var vm = this;
                consumer.saveOrUpdate(this.editForm.model)
                    .then(response => {
                        if (response.status == 200) {
                            vm.resetEditForm();
                            vm.reloadList();
                            editModal.hide();
                            AlertBox.Notify.Success('Cập nhật thành công');
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        }
                    });
            },
            destroy: function (nk_id) {
                var vm = this;
                AlertBox.Comfirm('Xác nhận',
                    'Bạn có đồng ý xóa',
                    function () {
                        // Ok
                        consumer.destroy(nk_id).then(response => {
                            vm.reloadList();
                            AlertBox.Notify.Success('Xóa thành công');
                        })
                        .catch(error => {
                            vm.reloadList();
                            AlertBox.Notify.Failure('Không thể xóa');
                        })
                    },
                    function () {

                    })
            }
        }
    }
</script>
