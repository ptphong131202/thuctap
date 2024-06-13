<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Quyết định
                <button class="btn btn-danger btn-flat btn-xs">
                    <a style="color: white;" href="javascript:void(0);"  title="Thêm mới" v-on:click="create()">
                    <i class="fa fa-plus"></i> Thêm mới
                    </a>
                </button>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Quyết định</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-header">

                            <!-- T.Phong -->
                            
                            <div class="box-tool pull-left">
                                <form method="get" style="max-width:300px; display: flex;">
                                        <div class="input-group">
                                            <label>Mã hoặc tên quyết định</label>
                                            <input style="width: 300px;" type="text" name="search" v-model="filter.search" class="form-control" placeholder="Tìm kiếm..">
                                            
                                        </div>
                                        <div class="input-group" style="margin-left: 50px">
                                            <label>Loại quyết định</label>
                                            <select style="height: 33px;" name="qd_loai" v-model="filter.qd_loai"> 
                                                    <option value="0">Quyết định thêm lớp</option>
                                                    <option value="3">Quyết định xét thi tốt nghiệp</option>
                                                    <option value="1">Quyết định sinh viên tốt nghiệp</option>
                                                    <option value="2">Quyết định xóa tên sinh viên</option>
                                            </select>
                                        </div>
                                    <span class="input-group-btn" style="    transform: translateY(24px);    margin-left: 30px;">
                                        <i class="fa fa-search"></i>
                                        <button type="submit" class="btn btn-default">Tìm </button>
                                    </span>
                                </form>
                            </div>
                                        <!--  -->
                        </div>
                        <div class="box-body">
                            <table class="table table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="w-3 text-center">#</th>
                                        <th>Mã</th>
                                        <th>Quyết định</th>
                                        <th>Ngày</th>
                                        <th>Loại</th>
                                        <th class="w-100-p text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(qd, index) in table.list.data" :key="qd.qd_id">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td>{{ qd.qd_ma }}</td>
                                        <td>{{ qd.qd_ten }}</td>
                                        <td>{{ qd.qd_ngay | moment }}</td>
                                        <td>
                                            <span v-if="qd.qd_loai == 0">Quyết định thêm lớp</span>
                                            <span v-if="qd.qd_loai == 1">Quyết định sinh viên tốt nghiệp</span>
                                            <span v-if="qd.qd_loai == 2">Quyết định xóa tên sinh viên</span>
                                            <span v-if="qd.qd_loai == 3">Quyết định xét thi tốt nghiệp</span>
                                        </td>
                                        <td class="text-left">
                                            <button type="button" class="btn bg-orange btn-sm" v-on:click="edit(qd.qd_id)"><i class="fa fa-pencil"></i></button>
                                            <!-- <button type="button" class="btn btn-danger btn-sm" 
                                                v-if="!(qd.lop_hoc_exists || qd.sinh_vien_exists)"
                                                v-on:click="destroy(qd.qd_id)">
                                                <i class="fa fa-trash"></i>
                                            </button> -->
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                v-on:click="destroyV2(qd.qd_id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <!-- T.Phong thêm nút xem chi tiết quyết định -->
                                            <a v-if="qd.qd_loai === 0 && qd.checklophoc === true" :href="'http://localhost/cea-3.0/public/lop-hoc?search=' + qd.qd_id">
                                                <button type="button" class="btn bg-purple btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </a>
                                            <a v-if="qd.qd_loai === 1 && qd.dxtn_id !== null"  :href="'http://localhost/cea-3.0/public/dot-xet-tot-nghiep/?dxtn_id=' + qd.dxtn_id + '&tunam=' + qd.dxtn_tunam + '&dennam=' + qd.dxtn_dennam + '&chuongtrinh=' + qd.dxtn_hdt_id">
                                                <button type="button" class="btn bg-purple btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </a>
                                            <a v-if="qd.qd_loai === 3 && qd.dt_id !== null"  :href="'http://localhost/cea-3.0/public/dot-thi?dt_id=' + qd.dt_id + '&&tunam=' + qd.dt_tunam + '&dennam=' + qd.dt_dennam + '&chuongtrinh=' + qd.dt_hdt_id">
                                                <button type="button" class="btn bg-purple btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </a>
                                            <a v-if="qd.qd_loai === 2 && qd.checkusser === true" :href="'http://localhost/cea-3.0/public/sinh-vien?search=' + qd.qd_id ">
                                                <button type="button" class="btn bg-purple btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </a>
                                            <!--  -->
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

        <div class="modal fade" id="quyet-dinh-edit-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{{ editForm.reference == Object ? 'Thêm quyết đinh mới' : editForm.reference.qd_ten }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                 <form-group :errors="editForm.errors" :field="'qd_loai'">
                                    <label>Loại Quyết định</label>
                                    <select   class="form-control" v-model="editForm.model.qd_loai" >
                                        <option value="0">Quyết định thêm lớp</option>
                                        <option value="1">Quyết định tốt nghiệp</option>
                                        <option value="2">Quyết định xóa tên</option>
                                    </select>
                                </form-group>
                            </div>
                            <div class="col-md-6">
                                <form-group :errors="editForm.errors" :field="'qd_ma'">
                                    <label>Số Quyết định</label>
                                    <input type="text" class="form-control" v-model="editForm.model.qd_ma" />
                                </form-group>
                            </div>
                            <div class="col-md-6">
                                <form-group :errors="editForm.errors" :field="'qd_ten'">
                                    <label>Ngày Quyết định</label>
                                    <input type="date" class="form-control" v-model="editForm.model.qd_ngay" />
                                </form-group>
                            </div>
                            <div class="col-md-12">
                                <form-group :errors="editForm.errors" :field="'qd_ten'">
                                    <label>Quyết định</label>
                                    <textarea class="form-control" v-model="editForm.model.qd_ten" />
                                </form-group>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fa fa-times-circle-o"></i> Hủy bỏ
                        </button>
                        <button type="button" class="btn bg-purple" v-on:click="save">
                            <i class="fa fa-save"></i> Lưu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const editModal = {
        modal: function () {
            return $('#quyet-dinh-edit-modal');
        },
        show: function () {
            this.modal().modal({ keyboard: false, backdrop: 'static' });
        },
        hide: function () {
            this.modal().modal('hide');
        }
    }

    const consumer = {
        getListQuyetDinh: function () {
            const url = 'http://localhost/cea-3.0/public/api/quyet-dinh' + window.location.search; /** Phong */
            return axios.get(url).then(response => response.data);
        },
        getQuyetDinh: function (qd_id) {
            const url = 'http://localhost/cea-3.0/public/api/quyet-dinh/' + qd_id;/** Phong */
            return axios.get(url).then(response => response.data);
        },
        checkUsed: function (qd_id) {
            const url = 'http://localhost/cea-3.0/public/api/quyet-dinh/check-used/' + qd_id;/** Phong */
            return axios.get(url).then(response => response.data);
        },
        saveOrUpdate: function (formData) {
            if (formData.qd_id == null) {
                return axios.post('http://localhost/cea-3.0/public/api/quyet-dinh', formData);/** Phong */
            } else {
                return axios.put('http://localhost/cea-3.0/public/api/quyet-dinh/' + formData.qd_id, formData);/** Phong */
            }
        },
        destroy: function (qd_id) {
            return axios.delete('http://localhost/cea-3.0/public/api/quyet-dinh/' + qd_id);/** Phong */
        }
    }

    const store = {
        table: {
            set list(data) {
                window.localStorage.setItem('qd.table.list', JSON.stringify(data));
            },
            get list() {
                return window.localStorage.getItem('qd.table.list') ? JSON.parse(window.localStorage.getItem('qd.table.list')) : {};
            }
        }
    }

    export default {
        mounted() {
            this.reloadList();
        },
        data() {
            return {
                filter: {
                    search: new URLSearchParams(window.location.search).get('search'),
                    // T.Phong thêm filter
                    qd_loai: new URLSearchParams(window.location.search).get('qd_loai'), 
                },
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
        filters: {
            moment: function (date) {
                if (date) {
                    return moment(date).format('DD/MM/yyyy');
                }
                return null;
            }
        },
        methods: {
            reloadList: function () {
                var vm = this;
                consumer.getListQuyetDinh().then(data => {
                    Vue.set(vm.table, 'list', data);
                    store.table.list = data;
                });
            },
            resetEditForm: function () {
                this.editForm = {
                    reference: {},
                    model: {
                        qd_loai: 0,
                    },
                };
                Vue.set(this.editForm, 'errors', {});
            },
            create: function () {
                this.resetEditForm();
                editModal.show();
            },
            save: function () {
                if (this.editForm.model.qd_id == null) {
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
            edit: function (qd_id) {
                this.resetEditForm();
                editModal.show();
                consumer.getQuyetDinh(qd_id).then(data => {
                    this.editForm.model = data;
                    this.editForm.reference = { ...this.editForm.model };
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
            // destroy: function (qd_id) {
            //     var vm = this;
            //     AlertBox.Comfirm('Xác nhận',
            //         'Bạn có đồng ý xóa',
            //         function () {
            //             // Ok
            //             consumer.destroy(qd_id).then(response => {
            //                 vm.reloadList();
            //                 AlertBox.Notify.Success('Xóa thành công');
            //             })
            //         },
            //         function () {

            //         })
            // },
            destroyV2: function (qd_id) {
                consumer.checkUsed(qd_id)
                    .then(res => {
                        let isUsed = res.lop_hoc_exists || res.sinh_vien_exists;
                        var confirmMessage = 'Bạn có đồng ý xóa';
                        if (isUsed) {
                            confirmMessage = 'Quyết định này đang được sử dụng, bạn có chắc muốn xóa đi không'
                        }
                        var vm = this;
                        AlertBox.Comfirm('Xác nhận',
                            confirmMessage,
                            function () {
                                // Ok
                                consumer.destroy(qd_id).then(response => {
                                    vm.reloadList();
                                    AlertBox.Notify.Success('Xóa thành công');
                                })
                            },
                            function () {

                            })
                    })
            },
        }
    }
</script>
