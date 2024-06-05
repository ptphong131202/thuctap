<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                {{ modelLopHoc.lh_ten }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li><a :href="parent_url">Lớp học</a></li>
                <li class="active">{{ modelLopHoc.lh_ma }}</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a :href="parent_url" class="btn btn-default" style="margin-bottom:6px;">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>
            <a href="javascript:void(0);" class="btn btn-danger " style="margin-bottom:6px" v-on:click="create()"><i class="fa fa-plus"/> Thêm sinh viên</a>
                    <a v-bind:href="router" class="btn btn-success " style="margin-bottom:6px;"><i class="fa fa-file-excel-o"></i> Thêm sinh viên bằng Excel</a>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-header">
                            <h3 class="box-title with-border" >
                                Danh sách sinh viên
                            </h3>
                        </div>
                        <div class="box-body table-responsive" style="padding-top:0px">
                            <div style="margin-bottom:6px">
                                <h3 style="display:inline">
                                    <button class="btn btn-flat bg-purple btn-xs"
                                        v-on:click="preOpenQuyetDinh(1)"
                                        v-if="checkedListSinhVien.length != 0">
                                        Quyết định tốt nghiệp ({{checkedListSinhVien.length}})
                                    </button>
                                    <button class="btn btn-flat btn-danger btn-xs" v-if="checkedListSinhVien.length != 0"
                                        v-on:click="preOpenQuyetDinh(2)">Quyết định xóa tên ({{checkedListSinhVien.length}})</button>
                                </h3>
                            </div>
                            <table class="table table-striped table-bordered no-margin">
                                <thead>
                                    <tr >
                                        <th class="w-3 text-center">
                                            <input type="checkbox" id="checkall" v-on:click="checkAll()">
                                        </th>
                                        <th class="w-3 text-center">#</th>
                                        <th class="w-3 text-center">MSHS/HSSV</th>
                                        <th class="text-center">
                                            Thông tin sinh viên
                                            <input type="text" name="search" id="txtSearch" class="form-control" placeholder="Tìm kiếm tên hoặc mã số"
                                                style="font-weight:normal"
                                                v-on:keyup="searchSinhVien">
                                        </th>
                                        <th class="w-10 text-center">Năm sinh</th>
                                        <th class="w-20 text-center">Liên hệ</th>
                                        <th class="w-20 text-center">Ghi chú</th>
                                        <th class="w-100-p text-left">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(sv) in listSinhVien.listFilter" :key="sv.sv_id">
                                        <td class="text-center">
                                            <input type="checkbox" :value="sv" v-model="checkedListSinhVien" v-on:change="kiemTraCheckAll()">
                                        </td>
                                        <td class="text-center">{{ listSinhVien.list.indexOf(sv)+1 }}</td>
                                        <td class="text-center">{{ sv.sv_ma }}</td>
                                        <td class="text-left">
                                            <strong>{{ sv.sv_ho }} {{sv.sv_ten}}</strong> ({{ sv.sv_gioitinh == 0 ? 'Nữ' : 'Nam' }})<br/>
                                            <span v-if=" sv.sv_dantoc != null && sv.sv_dantoc != '' "><strong>Dân tộc:</strong> {{ sv.sv_dantoc }}<br/></span>
                                            <span v-if=" sv.sv_trinhdo != null && sv.sv_trinhdo != '' "><strong>Trình độ:</strong> {{ sv.sv_trinhdo }}<br/></span>
                                        </td>
                                        <td class="text-center">
                                            {{ sv.sv_ngaysinh }}
                                        </td>
                                        <td>
                                            <span v-if=" sv.user.email != null && sv.user.email != '' "><strong><i class="fa fa-envelope"/></strong> {{ sv.user.email }}<br/></span>
                                            <strong><i class="fa fa-home"/></strong> {{ sv.sv_diachi }}<br/>
                                            <span v-if=" sv.sv_sdt != null && sv.sv_sdt != '' "><strong><i class="fa fa-phone"/></strong> {{ sv.sv_sdt }}<br/></span>
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
                                            <button type="button" class="btn bg-orange btn-sm" v-on:click="edit(sv.sv_id)"><i class="fa fa-pencil"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                v-if="sv.sinh_vien_bang_diem_count < 1"
                                                v-on:click="destroySinhVien(sv.sv_id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="listSinhVien.listFilter == null || listSinhVien.listFilter.length == 0">
                                        <td colspan="99" class="text-center">Không tìm thấy dữ liệu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer">
                            <a :href="'/lop-hoc'" class="btn btn-default">
                                <i class="fa fa-share"></i> Trở về danh sách
                            </a>
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
                        <form-group  :errors="editQuyetDinhForm.errors" :field="'qd_hocky'">
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
                                    <label>Nội dung quyết định</label>
                                    <textarea class="form-control" v-model="editQuyetDinhForm.model.qd_ten" />
                                </form-group>
                            </div>
                        </div>
                        <div>
                            <label>Các sinh viên {{ loai == 2 ? 'bị xóa tên' : 'tốt nghiệp' }}: </label> {{checkedListSinhVien.map(item => item.sv_ten).join(' ,')}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> Hủy bỏ</button>
                        <button type="button" class="btn bg-purple" v-on:click="saveQuyetDinh"><i class="fa fa-save"></i> Lưu</button>
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
                                    <label>Lớp học</label>
                                    <select class="form-control" v-model="editForm.model.lh_id">
                                        <option v-for="lh in listLopHoc.list" v-bind:value="lh.lh_id" :key="lh.lh_id">
                                            {{ lh.lh_ten }}
                                        </option>
                                    </select>
                                </form-group>
                                <form-group :errors="editForm.errors" :field="'sv_ma'">
                                    <label>MSSV/MSHS</label>
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
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_ten'">
                                            <label>Tên</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_ten" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
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
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_ngaysinh'">
                                            <label>Ngày sinh</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_ngaysinh" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_dantoc'">
                                            <label>Dân tộc</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_dantoc" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_trinhdo'">
                                            <label>Trình độ</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_trinhdo" />
                                        </form-group>
                                    </div>
                                </div>
                                <form-group :errors="editForm.errors" :field="'sv_diachi'">
                                    <label>Địa chỉ</label>
                                    <textarea  class="form-control" v-model="editForm.model.sv_diachi" />
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
                                            <label>Nội dung quyết định</label>
                                            <textarea v-model="editForm.model.qd_ten" class="form-control" />
                                        </form-group>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        getListSinhVien: function (lh_id) {
            const url = 'http://localhost/cea-2.0/public/api/lop-hoc/'+lh_id+'/ds-sinh-vien/';
            return axios.get(url).then(response => response.data);
        },
        getSinhVien: function (sv_id) {
            const url = 'http://localhost/cea-2.0/public/api/sinh-vien/' + sv_id;
            return axios.get(url).then(response => response.data);
        },
        getLopHoc: function (lh_id) {
            const url = 'http://localhost/cea-2.0/public/api/lop-hoc/' + lh_id;
            return axios.get(url).then(response => response.data);
        },
        saveOrUpdateSinhVien: function (formData) {
            if (formData.sv_id == null) {
                return axios.post('http://localhost/cea-2.0/public/api/sinh-vien', formData);
            } else {
                return axios.put('http://localhost/cea-2.0/public/api/sinh-vien/' + formData.sv_id, formData);
            }
        },
        destroySinhVien: function (sv_id) {
            return axios.delete('http://localhost/cea-2.0/public/api/sinh-vien/' + sv_id);
        },
        getListLopHoc: function () {
            const url = 'http://localhost/cea-2.0/public/api/lop-hoc/all';
            return axios.get(url).then(response => response.data);
        },
        getListQuyetDinh: function (loai) {
            const url = 'http://localhost/cea-2.0/public/api/quyet-dinh/all/'+loai;
            return axios.get(url).then(response => response.data);
        },
        saveOrUpdateQuyetDinh: function (formData) {
            return axios.put('http://localhost/cea-2.0/public/api/sinh-vien/cap-nhat-quyet-dinh-sinh-vien', formData);
        },
    }

    export default {
        props: [ 'lh_id', 'router', 'parent_url'],
        mounted() {
            this.reloadListSinhVien();
            this.reloadLopHoc();
            this.reloadListDm();
            this.reloadQuyetDinh();
        },
        data() {
            return {
                modelLopHoc: Object,
                loai: Object,//1 là tốt nghiệp 2 là xóa
                editQuyetDinhForm: {
                    model: Object,
                    errors: Object,
                },
                editForm: {
                    reference: Object,
                    model: Object,
                    errors: Object,
                },
                listSinhVien: {
                    list: [],
                    listFilter: [],
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
            reloadQuyetDinh: function(){
                var vm = this;
                consumer.getListQuyetDinh(2).then(data => {
                    let danhSachQd = [{ id: -1, text: 'Không chọn' },{ id: 0, text: 'Quyết định mới' }];
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
                    let danhSachQd = [{ id: -1, text: 'Không chọn' },{ id: 0, text: 'Quyết định mới' }];
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
            reloadListSinhVien: function () {
                var vm = this;
                vm.checkedListSinhVien = [];
                consumer.getListSinhVien(vm.lh_id).then(data => {
                    Vue.set(vm.listSinhVien, 'list', data);
                    vm.listSinhVien.listFilter = vm.listSinhVien.list;
                });
                console.log(vm.listSinhVien);
            },
            reloadListDm: function (){
                var vm = this;
                if(vm.listLopHoc.list.length == 0){
                    consumer.getListLopHoc().then(data => {
                        vm.listLopHoc.list = data;
                    });
                }
            },
            reloadLopHoc: function(){
                consumer.getLopHoc(this.lh_id).then(data => {
                    this.modelLopHoc = data;
                });
            },
            resetEditForm: function () {
                this.editForm = {
                    reference: Object,
                    model: {
                        sv_gioitinh: 1,
                        qd_id: this.modelLopHoc.qd_id,
                    },
                };
                this.editForm.model.lh_id = this.lh_id;
                Vue.set(this.editForm, 'errors', {});
            },
            resetQuyetDinhEditForm: function () {
                this.editQuyetDinhForm = {
                    model: {
                        qd_id: -1,
                        qd_totnghiep: -1,
                        qd_xoaten: -1,
                        qd_hocky: 1,
                    },
                };
                Vue.set(this.editQuyetDinhForm, 'errors', {});
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
                consumer.saveOrUpdateSinhVien(this.editForm.model)
                    .then(response => {
                        if (response.status == 200) {
                            vm.resetEditForm();
                            vm.reloadListSinhVien();
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
                    if(data.quyet_dinh_them_lop[0] != null){
                        // this.editForm.model.qd_id = data.quyet_dinh_them_lop[0].qd_id;
                        Vue.set(this.editForm.model, 'qd_id', data.quyet_dinh_them_lop[0].qd_id);
                    }else{
                        // this.editForm.model.qd_id = -1;
                        Vue.set(this.editForm.model, 'qd_id', -1);
                    }
                    this.editForm.reference = { ...this.editForm.model };
                    editModal.show();
                })
            },
            update: function () {
                var vm = this;
                consumer.saveOrUpdateSinhVien(this.editForm.model,this.lh_id)
                    .then(response => {
                        if (response.status == 200) {
                            vm.resetEditForm();
                            vm.reloadListSinhVien();
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
            destroySinhVien: function (sv_id) {
                var vm = this;
                AlertBox.Comfirm('Xác nhận',
                    'Bạn có đồng ý xóa',
                    function () {
                        // Ok
                        consumer.destroySinhVien(sv_id).then(response => {
                            vm.reloadListSinhVien();
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
                            vm.reloadListSinhVien();
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
                document.getElementById("checkall").checked = (this.checkedListSinhVien.length == this.listSinhVien.list.length);
            },
            checkAll: function () {
                if(document.getElementById("checkall").checked){
                    this.checkedListSinhVien = this.listSinhVien.list;
                }else{
                    this.checkedListSinhVien = [];
                }
            },
            searchSinhVien: function () {
                var search = document.getElementById("txtSearch").value.toLowerCase();
                this.listSinhVien.listFilter = this.listSinhVien.list.reduce((newArray, element) => {
                    if(element.sv_ma.toLowerCase().includes(search)){
                        newArray.push(element);
                    }else if((element.sv_ho.toLowerCase() + ' ' + element.sv_ten.toLowerCase() ).includes(search)){
                        newArray.push(element);
                    }
                    return newArray;
                 }, []);
            },
        }
    }
</script>
