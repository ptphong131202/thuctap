<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header" v-if="editForm.lopHoc != Object">
            <h1>
                Nhập điểm lớp {{ editForm.lopHoc.lh_ma }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Lớp học</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div style="margin-bottom:6px;">
                <a :href="'/nhap-diem/' + lh_id" class="btn btn-default" >
                    <i class="fa fa-share"></i> Trở về danh sách
                </a>
                <button type="button" class="btn bg-purple" v-on:click="actionSave">
                    <i class="fa fa-save"></i> Lưu
                </button>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-header with-border">
                            <div class="box-title">Điểm rèn luyện học kỳ {{ editForm.model.kdt_hocky }}</div>
                        </div>
                        <div class="box-body">
                            <div style="margin-bottom: 10px;">
                                <button type="button" class="btn btn-default" v-on:click="actionShowSelectFile">
                                    <i class="fa fa-file-excel-o"></i> Nhập từ tập tin excel
                                </button>
                                <a href="/sample/import-ren-luyen-sample.xls" class="btn btn-default" download><i class="fa fa-download"></i> Tải mẫu</a>
                            </div>
                            <div id="panel-diem">
                                <table class="table table-hover table-striped table-bordered no-margin">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="w-3 text-center">#</th>
                                            <th rowspan="2" class="text-center w-5">
                                                MSSV
                                                <input v-model="filter.sv_ma" v-on:keyup="changeFilter" type="text" class="form-control input-sm" placeholder="Tìm kiếm MSSV"/>
                                            </th>
                                            <th rowspan="2" colspan="2" class="text-center">
                                                Họ và tên
                                                <input v-model="filter.sv_hoten" v-on:keyup="changeFilter" type="text" class="form-control input-sm" placeholder="Tìm kiếm theo tên..."/>
                                            </th>
                                            <th rowspan="2" class="w-10 text-center" style="min-width: 85px">Điểm rèn luyện</th>
                                            <!-- <th colspan="2" class="w-20 text-center" style="min-width: 170px">Ghi chú</th> -->
                                        </tr>
                                        <!-- <tr>
                                            <th class="text-center">QĐ tốt nghiệp</th>
                                            <th class="text-center">QĐ xóa tên</th>
                                        </tr> -->
                                    </thead>
                                    <tbody>
                                        <tr v-for="(sv, indexSv) in editForm.model.data" :key="indexSv" v-show="sv.show">
                                            <td class="text-center">{{ indexSv + 1 }}</td>
                                            <td class="text-center">{{ sv.sv_ma }}</td>
                                            <td class="w-10">{{ sv.sv_ho }}</td>
                                            <td class="w-10">{{ sv.sv_ten }}</td>
                                            <td>
                                                <form-group :errors="editForm.errors" :field="'data.' + indexSv + '.svd_final'">
                                                    <input type="number" v-model="sv.svd_final" class="form-control"/>
                                                </form-group>
                                            </td>
                                            <!-- <td>
                                                <span v-if="sv.qdtn_ma" :title="sv.qdtn_ten">{{ sv.qdtn_ma }}, {{ sv.qdtn_ngay | moment }}</span>
                                            </td>
                                            <td>
                                                <span v-if="sv.qdxt_ma" :title="sv.qdxt_ten">{{ sv.qdxt_ma }}, {{ sv.qdxt_ngay | moment }}</span>
                                            </td> -->
                                        </tr>
                                        <tr v-if="editForm.model.data == null || editForm.model.data == 0">
                                            <td colspan="100" class="text-center">{{ editForm.model.data != null ? 'Không tìm thấy dữ liệu' : 'Đang tải' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="box-footer">
                            <input type="file"
                                    class="hidden"
                                    id="import-file"
                                    accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    v-on:change="actionSelectFile"/>
                            <a :href="'/nhap-diem/' + lh_id" class="btn btn-default">
                                <i class="fa fa-share"></i> Trở về danh sách
                            </a>
                            
                            <button type="button" class="btn bg-purple" v-on:click="actionSave">
                                <i class="fa fa-save"></i> Lưu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
</template>

<script>
    const consumer = {
        getLopHoc: function (lh_id) {
            const url = 'http://localhost/cea-2.1/public/api/lop-hoc/' + lh_id;
            return axios.get(url).then(response => response.data);
        },
        getBangDiem: function(lh_id, hocKy) {
            // bd_type = 2 là bảng điểm học kỳ
            const url = `http://localhost/cea-2.1/public/api/nhap-diem/${lh_id}/bang-diem?hocky=${hocKy}&bd_type=2`;
            return axios.get(url).then(response => response.data);
        },
        saveBangDiem: function (formData) {
            const url = `http://localhost/cea-2.1/public/api/nhap-diem/${formData.lh_id}/bang-diem`;
            return axios.post(url, formData);
        },
        uploadFile: function (file) {
            var formData = new FormData();
            var headers = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
            formData.append('excel_file', file);
            let url = 'http://localhost/cea-2.1/public/api/excel/import-ren-luyen-score';
            return axios.post(url, formData, headers);
        }
    }

    const panelDiem = {
        selector: function () {
            return '#panel-diem';
        },
        block: function () {
            AlertBox.Loading.Block(this.selector(), 'Đang tải');
        },
        unblock: function () {
            AlertBox.Loading.Unblock(this.selector());
        }
    }

    export default {
        props: ['lh_id', 'hoc_ky'],
        mounted() {
            this.loadLopHoc();
        },
        data() {
            return {
                filter: {
                    sv_ma: '',
                    sv_hoten: '',
                },
                editForm: {
                    lopHoc: Object,
                    model: {
                        bd_type: 2,
                        kdt_hocky: this.hoc_ky,
                        lh_id: this.lh_id,
                        data: [],
                    },
                    errors: Object,
                },
                excel: {
                    sheets: [],
                    seledtedSheet: Object,
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
            loadLopHoc: function () {
                var vm = this;
                // Load thông tin lớp học
                consumer.getLopHoc(this.lh_id).then(lopHoc => {
                    this.editForm.lopHoc = lopHoc;
                    
                    // Load danh sách học kỳ
                    this.reloadSinhVien();
                });
            },
            reloadSinhVien: function () {
                let lh_id = this.lh_id;
                let hocKy = this.editForm.model.kdt_hocky;
                panelDiem.block();
                consumer.getBangDiem(lh_id, hocKy)
                    .then(data => {
                        data.data = data.data.filter(item => {
                            let passXoaTen = false;
                            let passTotNghiep = false;
                            if (item.quyetDinhXoaTen == null || item.quyetDinhXoaTen.pivot.qd_hocky >= hocKy) {
                                passXoaTen = true;
                            }
                            if (item.quyetDinhTotNghiep == null || item.quyetDinhTotNghiep.pivot.qd_hocky >= hocKy) {
                                passTotNghiep = true;
                            }
                            return passXoaTen && passTotNghiep;
                        });
                         data.data.forEach(item => {
                            item.show = true;
                            item.svd_second_old = item.svd_second;
                        });
                        this.editForm.model = data;
                         panelDiem.unblock();
                    });
            },
            actionSave: function () {
                var vm = this;
                var data = {
                    bd_type: this.editForm.model.bd_type,
                    lh_id: this.lh_id,
                    kdt_hocky: this.editForm.model.kdt_hocky,
                    data: this.editForm.model.data,
                }

                consumer.saveBangDiem(data).then(response => {
                        vm.editForm.errors = {};
                        AlertBox.Notify.Success('Lưu thành công');
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        } else {
                            AlertBox.Notify.Failure('Cập nhật thất bại');
                        }
                    });
            },
            changeFilter: function () {
                this.editForm.model.data.forEach(item => {
                    let match = false;
                    let fullname = (item.sv_ho + ' ' + item.sv_ten).trim().toLowerCase();
                    item.show = fullname.includes(this.filter.sv_hoten.trim().toLowerCase()) && item.sv_ma.trim().toLowerCase().includes(this.filter.sv_ma.trim().toLowerCase());
                })
            },
            actionShowSelectFile: function () {
                document.getElementById('import-file').click();
            },
            actionSelectFile: function (event) {
                if (event.target.files.length) {
                    consumer.uploadFile(event.target.files[0])
                        .then((response) => {
                            this.excel.sheets = response.data;
                            if (this.excel.sheets.length > 1) {
                                selectSheetModel.show();
                            } if (this.excel.sheets.length == 1) {
                                this.actionSelectSheet(this.excel.sheets[0])
                            }
                        })
                        .catch(error => {
                            AlertBox.Notify.Failure('Nhập dữ liệu thất bại');
                            console.log(error);
                        });
                }
            },
            actionSelectSheet: function (sheet) {
                try {
                    this.editForm.model.data.forEach(sinhVien => {
                        let found = sheet.rows.find(row => {
                            return row.sv_ma.toUpperCase() === sinhVien.sv_ma.toUpperCase();
                        });
                        if (found) {
                            sinhVien.svd_final = found.svd_first;
                        }
                    });
                    if (this.excel.sheets.length > 1) {
                        selectSheetModel.hide();
                    }
                    document.getElementById('import-file').value = '';
                    AlertBox.Notify.Success('Nhạp dữ liệu thành công, vui lòng chọn Lưu để hoàn tất');
                } catch (error) {
                    AlertBox.Notify.Failure('Nhập dữ liệu thất bại');
                    console.log(error);
                }
            },
        },
    }
</script>
