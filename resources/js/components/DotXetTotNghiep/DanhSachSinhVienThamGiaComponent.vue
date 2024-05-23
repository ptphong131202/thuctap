<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Kết quả đợt xét tốt nghiệp {{ dotxettotnghiep.dxtn_ten }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Danh sách sinh viên tham gia</li>
            </ol>
            <!-- <a href="{{ route('export-ds-dat-diem-thi-tot-nghiep', ['lh_id'=> $lopHoc->lh_id, 'dt_id'=> $dotThi->dt_id]) }}"
                            class="btn bg-olive  no-margin">
                            <i class="fa fa-file-excel-o"></i> Xuất kết quả danh sách thi đạt tốt nghiệp test
                        </a> -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a :href="'/dot-xet-tot-nghiep/'+dotxettotnghiep.dxtn_id" class="btn btn-default" style="margin-bottom:6px;">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box">
                        <div class="box-body">
                            <form method="get" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Từ khóa</label>
                                            <input type="text" name="search" v-model="filter.search" class="form-control" placeholder="Tên, mã sinh viên hoặc lớp">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Loại danh sách</label>
                                        <select class="form-control" name="loai" v-model="filter.loai">
                                            <option value="true">Danh sách sinh viên thi đạt tốt nghiệp</option>
                                            <option value="false">Danh sách sinh viên không đạt tốt nghiệp</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" style="margin-top: 24px;">
                                        <button type="submit"  class="btn btn-default"><i class="fa fa-search"></i> Tìm kiếm</button>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                    <div class="col-md-10">
                                        <a v-if="loai_Params == true || loai_Params == 'true'" :href="'/dot-xet-tot-nghiep/'+ dotxettotnghiep.dxtn_id+'/danh-sach-sinh-vien-thi-dat-tn/export?loai=true'" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Xuất excel danh sách đạt tốt nghiệp</a>
                                        <a v-if="loai_Params == 'false'" :href="'/dot-xet-tot-nghiep/'+ dotxettotnghiep.dxtn_id+'/danh-sach-sinh-vien-thi-dat-tn/export?loai=false'" class="btn btn-danger"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Xuất excel danh sách không đạt</a>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-body table-responsive">
                            <table class="table table-striped table-bordered no-margin" style="margin-top:12px !important">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="w-3 text-center">#</th>
                                        <th rowspan="2" class="w-10 text-center">MSHS/MSSV</th>
                                        <th rowspan="2" >Họ</th>
                                        <th rowspan="2" >Tên</th>
                                        <th colspan="2" class="w-3 text-center">Ngày sinh</th>
                                        <th rowspan="2" class="text-center">Khóa lớp</th>
                                        <th rowspan="2" class="text-center">Ghi chú</th>
                                        <th rowspan="2" class="text-center">Hành động</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Nam</th>
                                        <th class="text-center">Nữ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(nn, nnIndex) in table.list" :key="'nn' +nnIndex">
                                        <td colspan="11" v-if="nn.nn_id != null"><b> {{nn.nn_index}}. {{ "Ngành/nghề " }} {{nn.nn_ten}} </b></td>
                                        <td v-if="nn.nn_id == null">
                                            {{nn.index + 1}}
                                        </td>
                                        <td v-if="nn.nn_id == null" class="text-center">
                                            {{nn.sv_ma}}
                                        </td>
                                        <td v-if="nn.nn_id == null">
                                            {{nn.sv_ho}}
                                        </td>
                                        <td v-if="nn.nn_id == null">
                                            {{nn.sv_ten}}
                                        </td>
                                        <td v-if="nn.nn_id == null">
                                            <span v-if="nn.sv_gioitinh == 1">{{ nn.sv_ngaysinh }}</span>
                                        </td>
                                        <td v-if="nn.nn_id == null">
                                            <span v-if="nn.sv_gioitinh == 0">{{ nn.sv_ngaysinh }}</span>
                                        </td>
                                        <td v-if="nn.nn_id == null">
                                            {{nn.lh_ten + ' K' + nn.kdt_khoa}}
                                        </td>
                                        <td v-if="nn.nn_id == null">
                                            {{nn.svxtn_ghichu}}
                                        </td>
                                        <td v-if="qd">
                                            <button v-if="nn.svxtn_dattn == 1 && nn.svxtn_vipham == NULL" class="btn btn-danger" v-on:click="showModal($event, nn.lh_id, nn.sv_id)" title="Không đạt tốt nghiệp">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </button>

                                            <button v-if="nn.svxtn_vipham == 1" class="btn btn-success" v-on:click="capNhatTrangThaiSinhVienDatTieuChuan(nn.lh_id, nn.sv_id)" title="Đạt tốt nghiệp">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="lydo-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Nhập lý do "{{ editForm.model.lydo_title }}"</h4>
                    </div>
                    <div class="modal-body">
                        <form-group :errors="editForm.errors" :field="'dt_ghichu'">
                            <label>Chi tiết lý do</label>
                            <input type="text" class="form-control" v-model="editForm.model.dt_ghichu" />
                        </form-group>
                    </div>
                    <div class="modal-footer">
                        <button type="button" v-on:click="capNhatTrangThaiSinhVien(editForm.model.lh_id, editForm.model.sv_id, editForm.model.dt_ghichu)" class="btn btn-danger">Lưu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Label from '../../../../vendor/laravel/breeze/stubs/inertia-vue/resources/js/Components/Label.vue';
    const lydoModal = {
        modal: function () {
            return $('#lydo-modal');
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
        getListSinhVien: function (dxtn_id) {
            const url = 'http://localhost/tthl/public/api/dot-xet-tot-nghiep/'+dxtn_id+'/danhsachsinhvien'+ window.location.search;
            return axios.get(url).then(response => response.data);
        },
        capNhatSinhVienDotThi: function (dt_id,formData) {
            return axios.post('http://localhost/tthl/public/api/dot-thi/'+dt_id+'/danhsachsinhvien', formData);
        },
        capNhatTrangThaiSinhVien: function (dxtn_id, dt_id, lh_id, sv_id, svd_ghichu, svxtn_vipham) {
            return axios.get('http://localhost/tthl/public/api/dot-xet-tot-nghiep/'+dxtn_id+'/cap-nhat-trang-thai-sv-dxtn?dt_id='+ dt_id +'&lhid='+lh_id+'&svid='+sv_id+'&svxtn_ghichu=' + svd_ghichu + '&svxtn_vipham=' + svxtn_vipham );
        },
        // xoaSinhVienThamGia: function (dt_id, lh_id, sv_id) {
        //     return axios.get('http://localhost/tthl/public/api/dot-thi/'+dt_id+'/xoa-sinh-vien-tham-gia?lhid='+lh_id+'&svid='+sv_id);
        // },
        exportDiemTheoLop: function (dt_id, lh_id) {
            return axios.get('/dot-thi/'+dt_id+'/diem-dot-thi-theo-lop/export?lhid='+lh_id);
        },
        getListQuyetDinh: function (loai) {
            const url = 'http://localhost/tthl/public/api/quyet-dinh/all/'+loai;
            return axios.get(url).then(response => response.data);
        },
        saveOrAddQuyetDinh: function (formData) {
            return axios.post('http://localhost/tthl/public/api/sinh-vien/them-quyet-dinh-thi-tot-nghiep', formData);
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
        props: [ 'dxtn_id', 'dxtn_ten', 'qd', 'dt_id', 'dt_ten' ],
        mounted() {
                Label
            this.reloadList();
            this.reloadQuyetDinh();
            this.loai_Params = new URLSearchParams(window.location.search).get('loai') == null ? true : (new URLSearchParams(window.location.search).get('loai'));
        },
        data() {
            return {
                loai_Params: Object,
                loai: Object,//1 là tốt nghiệp 2 là xóa
                editQuyetDinhForm: {
                    model: Object,
                    errors: Object,
                },
                filter: {
                    search: new URLSearchParams(window.location.search).get('search'),
                    loai: new URLSearchParams(window.location.search).get('loai') == null ? true : (new URLSearchParams(window.location.search).get('loai')),
                },
                editForm: {
                    reference: Object,
                    model: Object,
                    errors: Object,
                },
                dotthi: {
                    dt_id: this.dt_id,
                    dt_ten: this.dt_ten,
                },
                dotxettotnghiep: {
                    dxtn_id: this.dxtn_id,
                    dxtn_ten: this.dxtn_ten,
                    qd: this.qd
                },
                table: {
                    list: store.table.list,
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
                    // vm.listQuyetDinhXoaTen = {
                    //     select2: danhSachQd,
                    // }
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
            reloadList: function () {
                var vm = this;
                vm.checkedListSinhVien = [];
                consumer.getListSinhVien(this.dotxettotnghiep.dxtn_id).then(data => {
                    let formatData = [];
                    let totalNganhNghe = 1;
                    data.forEach(function (item) {
                        let nganhnghe = {
                            nn_index: totalNganhNghe++,
                            nn_ten: item.nn_ten,
                            nn_id: item.nn_id,
                            nn_ma: item.nn_ma,
                        }
                        formatData.push(nganhnghe);
                        item.danhsachsinhvien.forEach(function (sv, index) {
                            sv.index = index;
                            formatData.push(sv);
                        });
                    });
                    Vue.set(vm.table, 'list', formatData);
                    store.table.list = formatData;
                });
            },
            resetEditForm: function () {
                this.editForm = {
                    reference: Object,
                    model: {
                        lydo_title: '',
                        dt_ghichu: '',
                    },
                };
                Vue.set(this.editForm, 'errors', {});
            },
            resetQuyetDinhEditForm: function () {
                this.editQuyetDinhForm = {
                    model: {
                        qd_id: -1,
                        qd_totnghiep: -1
                    },
                };
                Vue.set(this.editQuyetDinhForm, 'errors', {});
            },
            preOpenQuyetDinh: function (loai){
                this.resetQuyetDinhEditForm();
                this.loai = loai;
                quyetQinhModal.show();
            },
            saveQuyetDinh: function (){
                var vm = this;
                var obj = this.editQuyetDinhForm.model;
                obj.dt_id = this.dt_id;
                // obj.loai = this.loai;
                obj.ds_sinhvien = this.checkedListSinhVien.map(item => item.sv_id);
                if (this.loai == 1) {
                    obj.qd_id = this.editQuyetDinhForm.model.qd_totnghiep;
                }
                consumer.saveOrAddQuyetDinh(obj)
                    .then(response => {
                        if (response.status == 200) {
                            vm.reloadList();
                            vm.reloadQuyetDinh();
                            quyetQinhModal.hide();
                            AlertBox.Notify.Success('Thêm thành công');
                        }
                    })
                    .catch(error => {
                        console.log("response nè: ", error);

                        if (error.response.status == 422) {
                            Vue.set(vm.editQuyetDinhForm, 'errors', error.response.data.errors);
                        }
                    });
            },
            kiemTraCheckAll: function () {
                document.getElementById("checkall").checked = (this.checkedListSinhVien.length == this.table.list.length);
            },
            checkAll: function () {
                if(document.getElementById("checkall").checked){
                    this.checkedListSinhVien = this.table.list;
                }else{
                    this.checkedListSinhVien = [];
                }
            },

            showModal: function (event, lh_id, sv_id) {
                const buttonText = event.target.textContent;
                this.editForm = {
                    reference: Object,
                    model: {
                        lh_id: lh_id,
                        sv_id: sv_id,
                        lydo_title: buttonText,
                        dt_ghichu: '',
                    },
                };
                Vue.set(this.editForm, 'errors', {});

                      // this.resetEditForm();
                lydoModal.show();
            },


            capNhatTrangThaiSinhVien: function (lh_id, sv_id, svd_ghichu) {
                const svxtn_vipham = 1;

                    consumer.capNhatTrangThaiSinhVien(this.dotxettotnghiep.dxtn_id, this.dotthi.dt_id, lh_id, sv_id, svd_ghichu, svxtn_vipham)
                        .then(response => {
                            if (response.status == 200) {
                                AlertBox.Notify.Success('cập nhật trạng thái thành công');
                                lydoModal.hide();
                                this.reloadList();
                            }
                        })
                        .catch(error => {
                            if (error.response.status == 422) {
                                Vue.set(vm.editForm, 'errors', error.response.data.errors);
                            }
                        });
            },
            capNhatTrangThaiSinhVienDatTieuChuan: function (lh_id, sv_id) {
                if(confirm('Bạn có chắc muốn cập nhật trạng thái sinh viên/học sinh "Đạt tốt nghiệp" không?')){
                    const svd_ghichu = "";
                    consumer.capNhatTrangThaiSinhVien(this.dotxettotnghiep.dxtn_id, this.dotthi.dt_id, lh_id, sv_id, svd_ghichu, null)
                        .then(response => {
                            if (response.status == 200) {
                                AlertBox.Notify.Success('cập nhật trạng thái thành công');
                                this.reloadList();
                            }
                        })
                        .catch(error => {
                            if (error.response.status == 422) {
                                Vue.set(vm.editForm, 'errors', error.response.data.errors);
                            }
                        });
                }
            },



            // xoaSinhVienThamGia: function (lh_id, sv_id) {
            //     if(confirm('Bạn có chắc muốn xóa sinh viên này ra khỏi đợt thi hiện tại chứ?')) {
            //         consumer.xoaSinhVienThamGia(this.dotthi.dt_id, lh_id, sv_id)
            //             .then(response => {
            //                 if (response.status == 200) {
            //                     AlertBox.Notify.Success('Cập nhật thành công');
            //                     this.reloadList();
            //                 }
            //             })
            //             .catch(error => {
            //                 if (error.response.status == 422) {
            //                     Vue.set(vm.editForm, 'errors', error.response.data.errors);
            //                 }
            //             });
            //     }
            // },
        }
    }
</script>
