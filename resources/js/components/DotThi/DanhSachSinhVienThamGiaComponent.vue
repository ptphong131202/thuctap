<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Đợt thi {{ dotthi.dt_ten }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Danh sách sinh viên tham gia</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a :href="'/dot-thi/'+dotthi.dt_id" class="btn btn-default" style="margin-bottom:6px;">
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
                                    <div class="col-md-2">
                                        <label>Loại thi</label>
                                        <select class="form-control" name="loai_thi" v-model="filter.loai_thi">
                                            <option value="-1">Tất cả</option>
                                            <option value="0">Báo cáo chuyên đề</option>
                                            <option value="1">Thi tốt nghiệp</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Loại danh sách</label>
                                        <select class="form-control" name="loai" v-model="filter.loai">
                                            <option value="true">Danh sách sinh viên đạt tiêu chuẩn tham gia đợt thi</option>
                                            <option value="false">Danh sách sinh viên không đạt tiêu chuẩn tham gia đợt thi</option>
                                            <option value="2">Danh sách sinh viên không tham gia đợt thi</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" style="margin-top: 24px;">
                                        <button type="submit"  class="btn btn-default"><i class="fa fa-search"></i> Tìm kiếm</button>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                    <div class="col-md-10">
                                        <a v-if="loai_Params == true || loai_Params == 'true'" :href="'/dot-thi/'+ dotthi.dt_id +'/danh-sach-sinh-vien/export?search=&loai_thi=-1&loai=true&type_excel=1'" class="btn btn-success "><i class="fa fa-file-excel-o" aria-hidden="true"></i> Xuất excel danh sách đạt chuẩn</a>
                                        <a v-if="loai_Params == 'false'" :href="'/dot-thi/'+ dotthi.dt_id +'/danh-sach-sinh-vien/export?search=&loai_thi=-1&loai=false&type_excel=2'" class="btn btn-danger"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Xuất excel danh sách không đạt chuẩn</a>
                                        <button class="btn bg-purple" v-on:click="capNhatSinhVienDotThi" v-if="qd && (loai_Params == true || loai_Params == 'true')"><i class="fa fa-floppy-o" aria-hidden="true"></i> Cập nhật loại thi</button>
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
                                        <th rowspan="2" class="text-center">Loại thi</th>
                                        <th rowspan="2" class="text-center">Hành động</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Nam</th>
                                        <th class="text-center">Nữ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(nn, nnIndex) in table.list" :key="'nn' +nnIndex">
                                        <td colspan="11" v-if="nn.nn_id != null" ref="nganhNghe"><b> {{nn.nn_index}} Ngành/Nghề {{nn.nn_ten}} </b></td>
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
                                            {{nn.svd_ghichu}}
                                        </td>
                                        <td v-if="nn.nn_id == null && nn.svd_khongdatloai != 0">
                                            <select class="form-control" v-model="nn.svd_loai" v-if="qd && nn.svd_lan == 1">
                                                <option value="0">Thi tốt nghiệp</option>
                                                <option value="1">Báo cáo chuyên đề</option>
                                            </select>
                                            <div class="form-control" v-if="!qd || nn.svd_lan != 1">
                                                <p v-if="nn.svd_loai == 0" value="0">Thi tốt nghiệp</p>
                                                <p v-if="nn.svd_loai == 1" value="1">Báo cáo chuyên đề</p>
                                            </div>
                                        </td>
                                        <td v-if="nn.nn_id == null && qd && nn.svd_khongdatloai != 0">
                                            <button v-if="nn.svd_dieukien == 1" class="btn btn-danger" v-on:click="showModal($event, nn.lh_id, nn.sv_id)" title="Không đạt tiêu chuẩn">
                                                <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                            </button>

                                            <button v-if="nn.svd_dieukien == 1" class='btn btn-warning' v-on:click="showModal($event, nn.lh_id, nn.sv_id)" title="Không dự thi">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </button>
                                            <button v-if="nn.svd_dieukien != 1" class="btn btn-success" v-on:click="capNhatTrangThaiSinhVienDatTieuChuan(nn.lh_id, nn.sv_id)">
                                                <i class="fa fa-check" aria-hidden="true"></i> {{ nn.svd_khongdatloai ==  2 ? "Dự thi" : "Đạt tiêu chuẩn" }}
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
                        <button type="button" v-on:click="capNhatTrangThaiSinhVien(editForm.model.lh_id, editForm.model.sv_id, editForm.model.dt_ghichu, editForm.model.lydo_title)" class="btn btn-danger">Lưu</button>
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
        getListSinhVien: function (dt_id) {
            const url = 'http://localhost/cea_4.0/public/api/dot-thi/'+dt_id+'/danhsachsinhvien'+ window.location.search;
            return axios.get(url).then(response => response.data);
        },
        capNhatSinhVienDotThi: function (dt_id,formData) {
            return axios.post('http://localhost/cea_4.0/public/api/dot-thi/'+dt_id+'/danhsachsinhvien', formData);
        },
        capNhatTrangThaiSinhVien: function (dt_id, lh_id, sv_id, loai, svd_ghichu, svd_khongdatloai) {
            return axios.get('http://localhost/cea_4.0/public/api/dot-thi/'+dt_id+'/cap-nhat-trang-thai-sinh-vien?lhid='+lh_id+'&svid='+sv_id+'&loai='+loai + '&svd_ghichu=' + svd_ghichu + '&svd_khongdatloai=' + svd_khongdatloai );
        },
        // xoaSinhVienThamGia: function (dt_id, lh_id, sv_id) {
        //     return axios.get('http://localhost/cea_4.0/public/api/dot-thi/'+dt_id+'/xoa-sinh-vien-tham-gia?lhid='+lh_id+'&svid='+sv_id);
        // },
        exportDiemTheoLop: function (dt_id, lh_id) {
            return axios.get('/dot-thi/'+dt_id+'/diem-dot-thi-theo-lop/export?lhid='+lh_id);
        },
        getListQuyetDinh: function (loai) {
            const url = 'http://localhost/cea_4.0/public/api/quyet-dinh/all/'+loai;
            return axios.get(url).then(response => response.data);
        },
        saveOrAddQuyetDinh: function (formData) {
            return axios.post('http://localhost/cea_4.0/public/api/sinh-vien/them-quyet-dinh-thi-tot-nghiep', formData);
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
        props: [ 'qd', 'dt_id', 'dt_ten' ],
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
                    loai_thi: new URLSearchParams(window.location.search).get('loai_thi') == null ? -1 : (new URLSearchParams(window.location.search).get('loai_thi')),
                },
                editForm: {
                    reference: Object,
                    model: Object,
                    errors: Object,
                },
                dotthi: {
                    dt_id: this.dt_id,
                    dt_ten: this.dt_ten,
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
                consumer.getListSinhVien(this.dotthi.dt_id).then(data => {
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
            capNhatSinhVienDotThi: function () {
                var vm = this;
                let rq = {data:vm.table.list};
                consumer.capNhatSinhVienDotThi(this.dotthi.dt_id, rq)
                    .then(response => {
                        if (response.status == 200) {
                            AlertBox.Notify.Success('Thêm thành công');
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        }
                    });
            },
            capNhatTrangThaiSinhVien: function (lh_id, sv_id, svd_ghichu, lydo_title) {
                let svd_ghichu_detail = "";
                svd_ghichu_detail = lydo_title.trim() == "Không đạt tiêu chuẩn" ? "Không đạt tiêu chuẩn: " + svd_ghichu : "Không dự thi: " + svd_ghichu;
                const svd_khongdatloai = lydo_title.trim() == "Không đạt tiêu chuẩn" ? 1: 2;

                    consumer.capNhatTrangThaiSinhVien(this.dotthi.dt_id, lh_id, sv_id, this.filter.loai, svd_ghichu_detail, svd_khongdatloai)
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
                if(confirm('Bạn có chắc muốn cập nhật trạng thái sinh viên/học sinh "Đạt tiêu chuẩn" không?')){
                    const svd_ghichu = "";
                    consumer.capNhatTrangThaiSinhVien(this.dotthi.dt_id, lh_id, sv_id, this.filter.loai, svd_ghichu, null)
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
