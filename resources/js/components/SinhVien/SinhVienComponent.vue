<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Sinh viên
                <small>
                    <a href="javascript:void(0);" class="btn btn-danger btn-flat btn-xs"
                            v-on:click="create()"
                            title="Thêm mới">
                        <i class="fa fa-plus"></i> Thêm mới
                    </a>
                </small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Sinh viên</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-header">
                            <div class="box-tool pull-right">
                                <form method="get" style="max-width:300px">
                                    <div class="input-group">
                                        <input type="text" name="search" v-model="filter.search" class="form-control" placeholder="Tìm kiếm..">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-default">Tìm</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                            <div style="margin-bottom:6px">
                                <h3 style="display:inline">
                                    <button class="btn btn-flat bg-purple btn-sm" 
                                        v-on:click="preOpenQuyetDinh(1)"
                                        v-if="checkedListSinhVien.length != 0">
                                        Quyết định tốt nghiệp ({{checkedListSinhVien.length}})
                                    </button>
                                    <button class="btn btn-flat btn-danger btn-sm" v-if="checkedListSinhVien.length != 0"
                                        v-on:click="preOpenQuyetDinh(2)">Quyết định xóa tên ({{checkedListSinhVien.length}})</button>
                                </h3>
                            </div>
                            <table class="table table-striped table-bordered no-margin">
                                <thead>
                                    <tr >
                                        <th rowspan="2" class="w-3 text-center">
                                            <input type="checkbox" id="checkall" v-on:click="checkAll()">
                                        </th>
                                        <th class="w-3 text-center">#</th>
                                        <th class="w-3 text-center">Mã sinh viên</th>
                                        <th class="text-center">Thông tin sinh viên</th>
                                        <th class="w-10 text-center">Lớp tham dự</th>
                                        <th class="w-7 text-center">Năm sinh</th>
                                        <th class="w-15 text-center">Liên hệ</th>
                                        <th class="w-15 text-center">Ghi chú</th>
                                        <th class="w-100-p text-left">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(sv, index) in table.list.data" :key="sv.sv_id">
                                        <td class="text-center">
                                            <input type="checkbox" :value="sv" v-model="checkedListSinhVien" v-on:change="kiemTraCheckAll()">
                                        </td>
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td class="text-center">{{ sv.sv_ma }}</td>
                                        <td class="text-left">
                                            <strong>{{ sv.sv_ho }} {{sv.sv_ten}}</strong> ({{ sv.sv_gioitinh == 0 ? 'Nữ' : 'Nam' }})<br/>
                                            <strong>Dân tộc:</strong> {{ sv.sv_dantoc }}<br/>
                                            <strong>Trình độ:</strong> {{ sv.sv_trinhdo }}<br/>
                                        </td>
                                        <td class="text-left">
                                            <div v-if="sv.lop_hoc != null">
                                                <div  v-for="item in sv.lop_hoc" :key="item.lh_id">
                                                    {{item.lh_ma}} - {{item.lh_ten}}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{ sv.sv_ngaysinh | moment }}
                                        </td>
                                        <td>
                                            <span v-if="sv.user != null && sv.user.email != '' && sv.user.email != null "><strong><i class="fa fa-envelope"/></strong> {{ sv.user.email }}<br/></span>
                                            <strong><i class="fa fa-home"/></strong> {{ sv.sv_diachi }}<br/>
                                        </td>
                                        <td>
                                            <span v-if="sv.quyet_dinh_tot_nghiep[0] != null">
                                                <strong>QĐ tốt nghiệp:</strong> {{ sv.quyet_dinh_tot_nghiep[0].qd_ma }}, {{ sv.quyet_dinh_tot_nghiep[0].qd_ngay | moment }}<br/>
                                            </span>
                                            <span v-if="sv.quyet_dinh_xoa_ten[0] != null">
                                                <strong>QĐ xóa tên:</strong> {{ sv.quyet_dinh_xoa_ten[0].qd_ma }}, {{ sv.quyet_dinh_xoa_ten[0].qd_ngay | moment }}<br/>
                                            </span>
                                            <span v-if="sv.sv_ghichu != null">
                                                <strong>Ghi chú:</strong> {{ sv.sv_ghichu }}
                                            </span>
                                        </td>
                                        <td class="text-left">
                                            <button type="button"
                                                    title="Thay đổi"
                                                    class="btn bg-orange btn-sm"
                                                    v-on:click="edit(sv.sv_id)">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button"
                                                    title="Xóa"
                                                    v-if="sv.sinh_vien_bang_diem_count < 1"
                                                    class="btn btn-danger btn-sm"
                                                    v-on:click="destroy(sv.sv_id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="table.list.data == null || table.list.data.length == 0">
                                        <td colspan="99" class="text-center">Không tìm thấy dữ liệu</td>
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
                        <h4 class="modal-title">{{ loai == 2 ? 'Quyết định xóa tên' : 'Quyết định tốt nghiệp' }}</h4>
                    </div>
                    <div class="modal-body">
                        <form-group :errors="editQuyetDinhForm.errors" :field="'qd_hocky'">
                            <label>Học kỳ</label>
                            <select class="form-control input-sm" v-model="editQuyetDinhForm.model.qd_hocky" >
                                <option value="1">Học kỳ 1 năm I</option>
                                <option value="2">Học kỳ 2 năm I</option>
                                <option value="3">Học kỳ 1 năm II</option>
                                <option value="4">Học kỳ 2 năm II</option>
                                <option value="5">Học kỳ 1 năm III</option>
                                <option value="6">Học kỳ 2 năm III</option>
                            </select>
                        </form-group>
                        <form-group v-show="loai == 1" :errors="editQuyetDinhForm.errors" :field="'qd_totnghiep'">
                            <label>Quyết định</label>
                            <select2 v-model="editQuyetDinhForm.model.qd_totnghiep" :options="listQuyetDinhTotNghiep.select2"></select2>
                        </form-group>
                        <form-group v-show="loai == 2" :errors="editQuyetDinhForm.errors" :field="'qd_xoaten'">
                            <label>Quyết định</label>
                            <select2 v-model="editQuyetDinhForm.model.qd_xoaten" :options="listQuyetDinhXoaTen.select2"></select2>
                        </form-group>

                        <div class="row" v-if="(editQuyetDinhForm.model.qd_totnghiep == 0 && loai == 1) || (editQuyetDinhForm.model.qd_xoaten == 0 && loai == 2)">
                            <div class="col-md-6">
                                <form-group :errors="editQuyetDinhForm.errors" :field="'qd_ma'">
                                    <label>Số Quyết định</label>
                                    <input type="text" class="form-control" v-model="editQuyetDinhForm.model.qd_ma" />
                                </form-group>
                            </div>
                            <div class="col-md-6">
                                <form-group :errors="editQuyetDinhForm.errors" :field="'qd_ngay'">
                                    <label>Ngày Quyết định</label>
                                    <input type="date" class="form-control" v-model="editQuyetDinhForm.model.qd_ngay" />
                                </form-group>
                            </div>
                            <div class="col-md-12">
                                <form-group :errors="editQuyetDinhForm.errors" :field="'qd_ten'">
                                    <label>Tên Quyết định</label>
                                    <textarea class="form-control" v-model="editQuyetDinhForm.model.qd_ten" />
                                </form-group>
                            </div>
                        </div>
                        <div>
                            <label>Các sinh viên {{ loai == 2 ? 'bị xóa tên' : 'tốt nghiệp' }}: </label> {{checkedListSinhVien.map(item => item.sv_ten).join(' ,')}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn bg-purple" v-on:click="saveQuyetDinh">Lưu</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="sinh-vien-edit-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{{ editForm.reference == Object ? 'Thêm Sinh viên' : editForm.reference.sv_ten }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <form-group :errors="editForm.errors" :field="'lh_id'">
                                    <label>Lớp học </label>
                                    <select2 v-model="editForm.model.lh_id" :options="listLopHoc.list"></select2>
                                </form-group>
                                <form-group :errors="editForm.errors" :field="'sv_ma'">
                                    <label>MSHS</label>
                                    <input type="text" class="form-control" v-model="editForm.model.sv_ma" />
                                </form-group>
                                <form-group :errors="editForm.errors" :field="'password'">
                                    <label>Mật khẩu</label>
                                    <input type="password" class="form-control" v-model="editForm.model.password" />
                                </form-group>
                                <div class="row">
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'email'">
                                            <label>Email</label>
                                            <input type="text" class="form-control" v-model="editForm.model.email" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_sdt'">
                                            <label>Số điện thoại</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_sdt" />
                                        </form-group>
                                    </div>
                                </div>
                                <form-group :errors="editForm.errors" :field="'sv_ghichu'">
                                    <label>Ghi chú</label>
                                    <textarea class="form-control" v-model="editForm.model.sv_ghichu" />
                                </form-group>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_ho'">
                                            <label>Họ</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_ho" />
                                        </form-group>
                                        <form-group :errors="editForm.errors" :field="'sv_gioitinh'">
                                            <label>Giới tính</label>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="sv_gioitinh" value="1" v-model="editForm.model.sv_gioitinh" />
                                                    Nam
                                                </label>
                                                <label style="margin-left: 15px">
                                                    <input type="radio" name="sv_gioitinh" value="0" v-model="editForm.model.sv_gioitinh">
                                                    Nữ
                                                </label>
                                            </div>
                                        </form-group>
                                        <form-group :errors="editForm.errors" :field="'sv_dantoc'">
                                            <label>Dân tộc</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_dantoc" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_ten'">
                                            <label>Tên</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_ten" />
                                        </form-group>
                                        <form-group :errors="editForm.errors" :field="'sv_ngaysinh'">
                                            <label>Ngày sinh</label>
                                            <input type="date" class="form-control" v-model="editForm.model.sv_ngaysinh" />
                                        </form-group>
                                        <form-group :errors="editForm.errors" :field="'sv_trinhdo'">
                                            <label>Trình độ</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_trinhdo" />
                                        </form-group>
                                    </div>
                                </div>
                                <form-group :errors="editForm.errors" :field="'sv_diachi'">
                                    <label>Địa chỉ</label>
                                    <input type="text" class="form-control" v-model="editForm.model.sv_diachi" />
                                </form-group>
                                <form-group :errors="editForm.errors" :field="'qd_id'">
                                    <label>Theo quyết định </label>
                                    <select2 v-model="editForm.model.qd_id" :options="listQuyetDinhThemLop.select2"></select2>
                                </form-group>
                                <div class="row" v-if="editForm.model.qd_id == 0">
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'qd_ma'">
                                            <label>Số quyết định</label>
                                            <input type="text" v-model="editForm.model.qd_ma" class="form-control" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'qd_ngay'">
                                            <label>Ngày quyết định</label>
                                            <input type="date" v-model="editForm.model.qd_ngay" class="form-control" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-12">
                                        <form-group :errors="editForm.errors" :field="'qd_ten'">
                                            <label>Tên quyết định</label>
                                            <textarea v-model="editForm.model.qd_ten" class="form-control" />
                                        </form-group>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn bg-purple" v-on:click="save">Lưu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const editModal = {
        modal: function () {
            return $('#sinh-vien-edit-modal');
        },
        show: function () {
            this.modal().modal({ keyboard: false, backdrop: 'static' });
        },
        hide: function () {
            this.modal().modal('hide');
        }
    }

    const quyetQinhModal = {
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
        getListSinhVien: function () {
            const url = 'http://localhost/cea-3.0/public/api/sinh-vien' + window.location.search;
            return axios.get(url).then(response => response.data);
        },
        getSinhVien: function (sv_id) {
            const url = 'http://localhost/cea-3.0/public/api/sinh-vien/' + sv_id;
            return axios.get(url).then(response => response.data);
        },
        saveOrUpdate: function (formData) {
            if (formData.sv_id == null) {
                return axios.post('http://localhost/cea-3.0/public/api/sinh-vien', formData);
            } else {
                return axios.put('http://localhost/cea-3.0/public/api/sinh-vien/' + formData.sv_id, formData);
            }
        },
        destroy: function (sv_id) {
            return axios.delete('http://localhost/cea-3.0/public/api/sinh-vien/' + sv_id);
        },
        getListLopHoc: function () {
            const url = 'http://localhost/cea-3.0/public/api/lop-hoc/all';
            return axios.get(url).then(response => response.data);
        },
        getListQuyetDinh: function (loai) {
            const url = 'http://localhost/cea-3.0/public/api/quyet-dinh/all/'+loai;
            return axios.get(url).then(response => response.data);
        },
        saveOrUpdateQuyetDinh: function (formData) {
            return axios.put('http://localhost/cea-3.0/public/api/sinh-vien/cap-nhat-quyet-dinh-sinh-vien', formData);
        },
    }

    const store = {
        table: {
            set list(data) {
                window.localStorage.setItem('sv.table.list', JSON.stringify(data));
            },
            get list() {
                return window.localStorage.getItem('sv.table.list') ? JSON.parse(window.localStorage.getItem('sv.table.list')) : {};
            }
        }
    }

    export default {
        mounted() {
            this.reloadList();
            this.reloadListDm();
            this.reloadQuyetDinh();
        },
        data() {
            return {
                filter: {
                    search: new URLSearchParams(window.location.search).get('search')
                },
                editForm: {
                    reference: Object,
                    model: {},
                    errors: Object,
                },
                loai: Object,//1 là tốt nghiệp 2 là xóa
                table: {
                    list: store.table.list,
                },
                listLopHoc: {
                    list: [],
                },
                listQuyetDinhThemLop: {
                    select2: [],
                },
                listQuyetDinhXoaTen: {
                    select2: [],
                },
                listQuyetDinhTotNghiep: {
                    select2: [],
                },
                editQuyetDinhForm: {
                    model: Object,
                    errors: Object,
                },
                checkedListSinhVien: []
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
                consumer.getListSinhVien().then(data => {
                    Vue.set(vm.table, 'list', data);
                    store.table.list = data;
                });
            },
            reloadListDm(){
                var vm = this;
                consumer.getListLopHoc().then(data => {
                    let danhSachlh = [{ id: 0, text: 'Chọn lớp học' }];
                    danhSachlh.push(...data.map(item => {
                        return {
                            id: item.lh_id,
                            text: item.lh_ten
                        }
                    }));
                    vm.listLopHoc.list = danhSachlh;
                });
            },
            resetEditForm: function () {
                this.editForm = {
                    reference: Object,
                    model: {
                        sv_gioitinh: 1,
                        qd_id: -1,
                        lh_id: 0,
                    },
                };
                Vue.set(this.editForm, 'errors', {});
            },
            create: function () {
                this.resetEditForm();
                editModal.show();
            },
            save: function () {
                if(this.editForm.model.lh_id == 0){
                    this.editForm.model.lh_id = null;
                }
                if(this.editForm.model.qd_id == null){
                    this.editForm.model.qd_id = -1;
                }
                if (this.editForm.model.sv_id == null) {
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
            edit: function (sv_id) {
                this.resetEditForm();
                consumer.getSinhVien(sv_id).then(data => {
                    this.editForm.model = data;
                    this.editForm.model.lh_id = data.lop_hoc[0].lh_id;
                    this.editForm.model.email = data.user.email;
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
            destroy: function (sv_id) {
                var vm = this;
                AlertBox.Comfirm('Xác nhận',
                    'Bạn có đồng ý xóa',
                    function () {
                        // Ok
                        consumer.destroy(sv_id).then(response => {
                            vm.reloadList();
                            AlertBox.Notify.Success('Xóa thành công');
                        })
                    },
                    function () {

                    })
            },

            preOpenQuyetDinh: function (loai){
                this.resetQuyetDinhEditForm();
                this.loai = loai;
                quyetQinhModal.show();
            },
            reloadQuyetDinh: function(){
                var vm = this;
                consumer.getListQuyetDinh(2).then(data => {
                    let danhSachQd = [{ id: 0, text: 'Quyết định mới' }];
                    danhSachQd.push(...data.map(item => {
                        return {
                            id: item.qd_id,
                            text: item.qd_ten
                        }
                    }));
                    vm.listQuyetDinhXoaTen = {
                        select2: danhSachQd,
                    }
                });
                consumer.getListQuyetDinh(1).then(data => {
                    let danhSachQd = [{ id: 0, text: 'Quyết định mới' }];
                    danhSachQd.push(...data.map(item => {
                        return {
                            id: item.qd_id,
                            text: item.qd_ten
                        }
                    }));
                    vm.listQuyetDinhTotNghiep = {
                        select2: danhSachQd,
                    }
                }); 
                consumer.getListQuyetDinh(0).then(data => {
                    let danhSachQd = [{ id: -1, text: 'Không chọn' },{ id: 0, text: 'Quyết định mới' }];
                    danhSachQd.push(...data.map(item => {
                        return {
                            id: item.qd_id,
                            text: item.qd_ten
                        }
                    }));
                    vm.listQuyetDinhThemLop = {
                        select2: danhSachQd,
                    }
                });
            },
            resetQuyetDinhEditForm: function () {
                this.editQuyetDinhForm = {
                    model: {
                        qd_id: 0,
                        qd_totnghiep: 0,
                        qd_xoaten: 0,
                        qd_hocky: 1,
                    },
                };
                Vue.set(this.editQuyetDinhForm, 'errors', {});
            },
            saveQuyetDinh: function (){
                var vm = this;
                var obj = this.editQuyetDinhForm.model;
                obj.loai = this.loai;
                obj.ds_sinhvien = this.checkedListSinhVien.map(item => item.sv_id);
                if (this.loai == 1) {
                    obj.qd_id = this.editQuyetDinhForm.model.qd_totnghiep;
                } else {
                    obj.qd_id = this.editQuyetDinhForm.model.qd_xoaten;
                }
                consumer.saveOrUpdateQuyetDinh(obj)
                    .then(response => {
                        if (response.status == 200) {
                            vm.reloadList();
                            vm.reloadQuyetDinh();
                            quyetQinhModal.hide();
                            AlertBox.Notify.Success('Thêm thành công');
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editQuyetDinhForm, 'errors', error.response.data.errors);
                        }
                    });
            },
            kiemTraCheckAll: function () {
                document.getElementById("checkall").checked = (this.checkedListSinhVien.length == this.table.list.data.length);
            },
            checkAll: function () {
                if(document.getElementById("checkall").checked){
                    this.checkedListSinhVien = this.table.list.data;
                }else{
                    this.checkedListSinhVien = [];
                }
            }
        },
        
    }
</script>
