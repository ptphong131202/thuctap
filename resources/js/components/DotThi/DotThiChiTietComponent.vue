<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Đợt thi {{ dotthi.dt_ten }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Đợt thi</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a :href="parent_url" class="btn btn-default" style="margin-bottom:6px;">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-body">
                            <form method="get" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mã hoặc tên lớp</label>
                                            <input type="text" name="search" v-model="filter.search" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Niên khóa</label>
                                            <select name="nienkhoa" class="form-control" v-model="filter.nienkhoa" >
                                                <option value="-1">Tất cả</option>
                                                <option v-for="nk in listNienKhoa.select2" :key="nk.id" v-bind:value="nk.id">
                                                    {{nk.text}}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Hệ đào tạo</label>
                                            <select name="chuongtrinh" class="form-control" v-model="filter.chuongtrinh" >
                                                <option value="-1">Tất cả</option>
                                                <option v-for="nk in listHeDaoTao.select2" :key="nk.id" v-bind:value="nk.id">
                                                    {{nk.text}}
                                                </option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <div class="col-md-2" style="margin-top: 25px;">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Tìm kiếm</button>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="pull-right">
                                        <div class="col-md-12">
                                            <a class="btn btn-success" :href="'/dot-thi/'+ dotthi.dt_id +'/danh-sach-sinh-vien/export?search=&loai_thi=-1&loai=false&type_excel=0'"><i class="fa fa-download" aria-hidden="true"></i> Xuất excel DSSV thi lại</a>
                                            <a class="btn btn-primary" style="margin-left: 1rem;" v-bind:href="dotthi.dt_id+'/danh-sach-sinh-vien'"><i class="fa fa-file-text-o" aria-hidden="true"></i> Kết quả xét</a>
                                        </div>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-body table-responsive">
                            <table class="table table-striped table-bordered no-margin" style="margin-top:12px !important">
                                <thead>
                                    <tr>
                                        <th class="w-3 text-center">#</th>
                                        <th class="w-7">Mã lớp</th>
                                        <th>Lớp học</th>
                                        <th>Khóa</th>
                                        <th class="w-10">Niên khóa</th>
                                        <th class="w-16">Chương trình đào tạo</th>
                                        <!-- <th>Quyết định</th> -->
                                        <th class="w-17 text-center">Hành động</th>
                                        <!-- <th class="w-3 text-center"></th> -->
                                        <!-- <th class="w-100-p text-center">Điểm đợt thi</th> -->
                                        <!-- <th class="w-100-p text-center">Nhập điểm thi</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(lh, index) in table.list.data" :key="lh.lh_id">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td>{{ lh.lh_ma }}</td>
                                        <td>{{ lh.lh_ten }}</td>
                                        <td>{{ lh.khoa_dao_tao.kdt_khoa }}</td>
                                        <td>{{ lh.nien_khoa.nk_ten }}</td>
                                        <td>{{ lh.khoa_dao_tao.kdt_ten }}</td>
                                        <!-- <td>
                                            <span v-if="lh.quyet_dinh" :title="lh.quyet_dinh.qd_ten">{{ lh.quyet_dinh.qd_ma }}, {{ lh.quyet_dinh.qd_ngay | moment }}</span>
                                        </td> -->
                                        <td class="text-left">
                                            <a :href="lh.ketqua_url"
                                                title="Danh sách dự xét" class="btn bg-purple btn-sm"><i class="fa fa-eye"></i>
                                            </a>
                                            <a :href="lh.monhoc_url"
                                                title="Nhập điểm" class="btn bg-green btn-sm"><i class="fa fa-list-ol"></i>
                                            </a>
                                            <a :href="'/dot-thi/ket-qua-diem-thi-tn/' + lh.lh_id + '/' + dt_id"
                                                title="Xem kết quả thi" class="btn bg-blue btn-sm"><i class="fa fa-list-alt"></i>
                                            </a>
                                            <!-- <a :href="lh.xoa_url" title="Xóa" class="btn btn-danger btn-sm" v-if="qd">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <a title="Xóa" class="btn btn-danger btn-sm" v-if="!qd" style="cursor: not-allowed">
                                                <i class="fa fa-trash"></i>
                                            </a> -->
                                            <button type="button" class="btn btn-danger btn-sm" title="Xóa"  v-if="qd"
                                                 v-on:click="destroy(lh.lh_id, dt_id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" title="Xóa" v-if="!qd" style="cursor: not-allowed">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                        <!-- <td>
                                            <a :href="lh.export_diem_url"
                                                title="Xem danh sách học kỳ và môn học"
                                                class="btn btn-success btn-sm">
                                                <i class="fa fa-file-excel-o"></i> Tải xuống
                                            </a>
                                        </td> -->
                                        <!-- <td class="text-left">
                                            <a :href="lh.import_excel_url"
                                                title="Nhập điễm từ file excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Nhập điểm
                                            </a>
                                        </td> -->
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
    </div>
</template>

<script>
import Label from '../../../../vendor/laravel/breeze/stubs/inertia-vue/resources/js/Components/Label.vue';
    const editModal = {
        modal: function () {
            return $('#lop-hoc-edit-modal');
        },
        show: function () {
            this.modal().modal({ keyboard: false, backdrop: 'static' });
        },
        hide: function () {
            this.modal().modal('hide');
        }
    }

    const consumer = {
        getListLopHoc: function (dt_id) {
            const url = 'http://localhost/cea-2.0/public/api/lop-hoc/dot-thi-lop-hoc/'+dt_id+'/'+ window.location.search;
            return axios.get(url).then(response => response.data);
        },
        getLopHoc: function (lh_id) {
            const url = 'http://localhost/cea-2.0/public/api/lop-hoc/' + lh_id;
            return axios.get(url).then(response => response.data);
        },
        saveOrUpdate: function (formData) {
            if (formData.lh_id == null) {
                return axios.post('http://localhost/cea-2.0/public/api/lop-hoc', formData);
            } else {
                return axios.put('http://localhost/cea-2.0/public/api/lop-hoc/' + formData.lh_id, formData);
            }
        },
        destroy: function (lh_id, dt_id) {
            return axios.delete('http://localhost/cea-2.0/public/apidot-thi/xoa-lop-hoc/' + lh_id + '/' + dt_id);
        },
        getListKhoaDaoTao: function () {
            const url = 'http://localhost/cea-2.0/public/api/khoa-dao-tao/all';
            return axios.get(url).then(response => response.data);
        },
        getListQuyetDinh: function () {
            const url = 'http://localhost/cea-2.0/public/api/quyet-dinh/all/0';
            return axios.get(url).then(response => response.data);
        },
        getListNienKhoa: function () {
            const url = 'http://localhost/cea-2.0/public/api/nien-khoa/all';
            return axios.get(url).then(response => response.data);
        },
        getListHeDaoTao: function () {
            const url = 'http://localhost/cea-2.0/public/api/he-dao-tao/all';
            return axios.get(url).then(response => response.data);
        },
    }

    const store = {
        table: {
            set list(data) {
                window.localStorage.setItem('lh.table.list', JSON.stringify(data));
            },
            get list() {
                return window.localStorage.getItem('lh.table.list') ? JSON.parse(window.localStorage.getItem('lh.table.list')) : {};
            }
        }
    }

    export default {
        props: [ 'qd', 'dt_id', 'dt_ten', 'parent_url'],
        mounted() {
                Label
            this.reloadList();
            this.reloadListDm();
        },
        data() {
            return {
                previousUrl: this.previousUrl,
                filter: {
                    search: new URLSearchParams(window.location.search).get('search'),
                    nienkhoa: new URLSearchParams(window.location.search).get('nienkhoa') == null ? -1 : (new URLSearchParams(window.location.search).get('nienkhoa')),
                    chuongtrinh: new URLSearchParams(window.location.search).get('chuongtrinh') == null ? -1 : (new URLSearchParams(window.location.search).get('chuongtrinh')),
                },
                dotthi: {
                    dt_id: this.dt_id,
                    dt_ten: this.dt_ten,
                    qd: this.qd
                },
                editForm: {
                    reference: Object,
                    model: Object,
                    errors: Object,
                },
                table: {
                    list: store.table.list,
                },
                listNienKhoa: {
                    select2: []
                },
                listKhoaDaoTao: {
                    select2: [],
                },
                listHeDaoTao: {
                    select2: [],
                },
                listQuyetDinh: {
                    select2: [],
                },
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
                consumer.getListLopHoc(this.dotthi.dt_id).then(data => {
                    Vue.set(vm.table, 'list', data);
                    store.table.list = data;
                });
            },
            reloadListDm(){
                var vm = this;
                if(vm.listNienKhoa.select2.length == 0){
                    consumer.getListNienKhoa().then(data => {
                        vm.listNienKhoa.select2 = data.map(item => {
                            return {
                                id: item.nk_id,
                                text: item.nk_ten
                            }
                        });
                    });
                }

                if(vm.listKhoaDaoTao.select2.length == 0){
                    consumer.getListKhoaDaoTao().then(data => {
                        vm.listKhoaDaoTao.select2 = data.map(item => {
                            return {
                                id: item.kdt_id,
                                text: item.kdt_ten
                            }
                        });
                    });
                }

                if(vm.listQuyetDinh.select2.length == 0){
                    consumer.getListQuyetDinh().then(data => {
                        vm.listQuyetDinh.select2.push({ id: -1, text: 'Không chọn' });
                        vm.listQuyetDinh.select2.push({ id: 0, text: 'Quyết định mới' });
                        vm.listQuyetDinh.select2.push(...data.map(item => {
                            return {
                                id: item.qd_id,
                                text: item.qd_ten
                            }
                        }));
                    });
                }

                if(vm.listHeDaoTao.select2.length == 0){
                    consumer.getListHeDaoTao().then(data => {
                        vm.listHeDaoTao.select2 = data.map(item => {
                            return {
                                id: item.hdt_id,
                                text: item.hdt_ten
                            };
                        });
                    });
                }

            },
            resetEditForm: function () {
                this.editForm = {
                    reference: Object,
                    model: {
                        qd_loai:0,//quyết định thêm lớp
                        qd_id:-1,
                    },
                };
                if (this.listNienKhoa.select2.length > 0) {
                    this.editForm.model.nk_id = this.listNienKhoa.select2[0].id;
                }
                if (this.listKhoaDaoTao.select2.length > 0) {
                    this.editForm.model.kdt_id = this.listKhoaDaoTao.select2[0].id;
                }
                if (this.listQuyetDinh.select2.length > 0) {
                    this.editForm.model.qd_id = this.listQuyetDinh.select2[0].id;
                }
                Vue.set(this.editForm, 'errors', {});
            },
            create: function () {
                this.resetEditForm();
                editModal.show();
            },
            save: function () {
                if (this.editForm.model.lh_id == null) {
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
            edit: function (lh_id) {
                this.resetEditForm();
                editModal.show();
                consumer.getLopHoc(lh_id).then(data => {
                    this.editForm.model = data;
                    this.editForm.reference = { ...this.editForm.model };
                })
            },
            update: function () {
                var vm = this;
                consumer.saveOrUpdate(this.editForm.model)
                    .then(response => {
                        if (response.status == 200) {
                            consumer.getListQuyetDinh().then(data => {
                                vm.listQuyetDinh.select2.push({id: 0, text: 'Quyết định mới'});
                                vm.listQuyetDinh.select2.push(...data.map(item => {
                                    return {
                                        id: item.qd_id,
                                        text: item.qd_ten
                                    }
                                }));
                            });
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
            destroy: function (lh_id, dt_id) {
                var vm = this;
                AlertBox.Comfirm('Xác nhận',
                    'Bạn có đồng ý xóa',
                    function () {
                        // Ok
                        consumer.destroy(lh_id, dt_id).then(response => {
                            vm.reloadList();
                            AlertBox.Notify.Success('Xóa thành công');
                        })
                    },
                    function () {

                    })
            }
        }
    }
</script>
