<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header" v-if="editForm.lopHoc != Object">
            <h1>
                Nhập điểm lớp {{ editForm.lopHoc.lh_ma }}
                <small>
                    Đợt thi {{ editForm.model.dot_thi }}
                </small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Lớp học</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div style="margin-bottom:6px;">
                <a onclick="history.back()" class="btn btn-default" >
                    <i class="fa fa-share"></i> Trở về danh sách
                </a>
                <button type="button" class="btn bg-purple" v-on:click="actionSave">
                    <i class="fa fa-save"></i> Lưu
                </button>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-header with-border">
                            <div class="box-title">{{ editForm.monHoc.mh_ma }} - {{ editForm.monHoc.mh_ten }}</div>
                        </div>
                        <div class="box-body table-responsive">
                            <div style="margin-bottom: 10px;">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default" v-on:click="actionShowSelectFile"><i class="fa fa-file-excel-o"></i> Nhập từ tập tin excel</button>
                                    <a href="/sample/import-thi-tn-theo-tung-mon-sample.xls" class="btn btn-default" download>
                                        <i class="fa fa-download"></i> Tải mẫu
                                    </a>
                                    <!-- <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="javascript:void(0)" v-on:click="actionShowSelectFile(true)">Tập tin chứa công thức</a></li>
                                        <li><a href="javascript:void(0)" v-on:click="actionShowSelectFile(false)">Tập tin không chứa công thức</a></li>
                                    </ul> -->
                                </div>
                            </div>
                            <div id="panel-diem">
                                <table class="table table-hover table-striped table-bordered no-margin">
                                    <thead>
                                        <tr>
                                            <th class="w-3 text-center">#</th>
                                            <th class="text-center w-5">
                                                MSHS/MSSV
                                                <input v-model="filter.sv_ma" v-on:keyup="changeFilter" type="text" class="form-control input-sm" placeholder="Tìm kiếm MSSV"/>
                                            </th>
                                            <th  colspan="2" class="text-center">
                                                Họ và tên
                                                <input v-model="filter.sv_hoten" v-on:keyup="changeFilter" type="text" class="form-control input-sm" placeholder="Tìm kiếm theo tên..."/>
                                            </th>
                                            <th>Điểm thi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(sv, indexSv) in editForm.model.data" :key="indexSv" >
                                            <td class="text-center">{{ indexSv + 1 }}</td>
                                            <td class="text-center">{{ sv.sv_ma }}</td>
                                            <td class="w-10">{{ sv.sv_ho }}</td>
                                            <td class="w-10">{{ sv.sv_ten }}</td>
                                            <td>
                                                <div style="min-width: 100px;">
                                                    <form-group :errors="editForm.errors" :field="'data.' + indexSv + '.svd_first'">
                                                        <input type="number" v-model="sv.svd_first" class="form-control"/>
                                                    </form-group>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="editForm.model.data == null || editForm.model.data == 0">
                                            <td colspan="100" class="text-center">{{ editForm.model.data != null ? 'Không tìm thấy dữ liệu' : 'Đang tải' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a onclick="history.back()" class="btn btn-default">
                                <i class="fa fa-share"></i> Trở về danh sách
                            </a>
                            <input type="file"
                                    class="hidden"
                                    id="import-file"
                                    accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    v-on:change="actionSelectFile"/>
                            <input type="file"
                                    class="hidden"
                                    id="import-file-raw"
                                    accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    v-on:change="actionSelectFile"/>
                            <button type="button" class="btn bg-purple" v-on:click="actionSave">
                                <i class="fa fa-save"></i> Lưu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
        <div id="select-sheet" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Tập tin của bạn có nhiều sheet, vui lòng chọn sheet muốn nhập</h4>
                    </div>
                    <div class="modal-body">
                        <button v-for="(sheet, sIndex) in excel.sheets"
                                :key="sIndex"
                                style="text-align: left"
                                class="btn btn-primary btn-block"
                                v-on:click="actionSelectSheet(sheet)">
                            {{ sheet.number }}. {{ sheet.title }} ({{sheet.rows.length}})
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const consumer = {
        getLopHoc: function (lh_id) {
            const url = 'http://localhost/tthl/public/api/lop-hoc/' + lh_id;
            return axios.get(url).then(response => response.data);
        },
        getMonHoc: function (mh_id) {
            const url = 'http://localhost/tthl/public/api/mon-hoc/' + mh_id;
            return axios.get(url)
                    .then(response => response.data);
        },
        getBangDiem: function(lh_id, mh_id, dt_id) {
            // bd_type = 1 là bảng điểm môn học
            const url = `http://localhost/tthl/public/api/nhap-diem/${lh_id}/bang-diem-dot-thi?mh_id=${mh_id}&dt_id=${dt_id}`;
            return axios.get(url).then(response => response.data);
        },
        saveBangDiem: function (formData) {
            const url = `http://localhost/tthl/public/api/nhap-diem/${formData.lh_id}/bang-diem-dot-thi`;
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
            const url = 'http://localhost/tthl/public/apiexcel/import-diemdotthi-theomon';
            return axios.post(url, formData, headers);
        }
    }

    const selectSheetModel = {
        modal: function () {
            return $('#select-sheet');
        },
        show: function () {
            this.modal().modal({ keyboard: false, backdrop: 'static' });
        },
        hide: function () {
            this.modal().modal('hide');
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

    const BANGDIEMTYPE = {
        MONHOC: 1,
        HOCKY: 2,
    }

    export default {
        props: ['lh_id', 'dot_thi', 'dt_id', 'mh_id'],
        mounted() {
            this.loadLopHoc();
        },
        updated: function() {
            this.fixEditDate();
        },
        data() {
            return {
                filter: {
                    sv_ma: '',
                    sv_hoten: '',
                },
                editForm: {
                    lopHoc: Object,
                    monHoc: Object,
                    model: {
                        bd_type: 1,
                        dot_thi: this.dot_thi,
                        dt_id: this.dt_id,
                        mh_id: this.mh_id,
                        lh_id: this.lh_id,
                        data: [],
                    },
                    errors: Object
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
            fixEditDate: function () {
                $('.datetimepicker').datetimepicker({
                    format: 'DD/MM/YYYY'
                });
            },
            loadLopHoc: function () {
                var vm = this;
                // Load thông tin lớp học
                consumer.getLopHoc(this.lh_id).then(lopHoc => {
                    this.editForm.lopHoc = lopHoc;

                    // Load danh sách học kỳ
                    this.reloadSinhVien();
                });

                consumer.getMonHoc(this.mh_id).then(monHoc => {
                    this.editForm.monHoc = monHoc;
                });
            },
            reloadSinhVien: function () {
                let lh_id = this.lh_id;
                let mh_id = this.editForm.model.mh_id;
                let dotThi = this.editForm.model.dt_id;
                panelDiem.block();
                consumer.getBangDiem(lh_id, mh_id, dotThi)
                    .then(data => {

                        let dateOptions = { year: 'numeric', month: '2-digit', day: '2-digit' };
                        if (data.dt_bd_tungay) {
                            data.dt_bd_tungay_formated = moment(data.dt_bd_tungay, "YYYY-MM-DD").format('DD/MM/YYYY');
                        }

                        if (data.dt_bd_denngay) {
                            data.dt_bd_denngay_formated = moment(data.dt_bd_denngay, "YYYY-MM-DD").format('DD/MM/YYYY');
                        }
                        console.log(data);
                        this.editForm.model = data;
                        panelDiem.unblock();
                    });
            },
            actionSave: function () {
                var vm = this;

                var data = {
                    lh_id: this.lh_id,
                    mh_id: this.editForm.model.mh_id,
                    dt_id: this.dt_id,
                    dt_bd_tungay: null,
                    dt_bd_denngay: null,
                    data: this.editForm.model.data,
                }

                console.log(data);

                consumer.saveBangDiem(data).then(response => {
                        vm.editForm.errors = {};
                        AlertBox.Notify.Success('Lưu thành công');
                    })
                    .catch(error => {
                        console.log(error.response);
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        } else {
                            AlertBox.Notify.Failure('Cập nhật thất bại');
                        }
                    });
            },
            actionShowSelectFile: function (cal) {
                if (cal) {
                    document.getElementById('import-file').click();
                } else {
                    document.getElementById('import-file-raw').click();
                }
            },
            actionSelectFile: function (event) {
                if (event.target.files.length) {

                    consumer.uploadFile(event.target.files[0])
                        .then((response) => {
                            // console.log("res: ", response.data);
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
                            sinhVien.svd_first = found.svd_first;
                            sinhVien.svd_second = found.svd_second;
                            sinhVien.svd_ghichu = found.svd_ghichu;
                        }
                    });
                    if (this.excel.sheets.length > 1) {
                        selectSheetModel.hide();
                    }
                    document.getElementById('import-file').value = '';
                    document.getElementById('import-file-raw').value = '';
                    AlertBox.Notify.Success('Nhạp dữ liệu thành công, vui lòng chọn Lưu để hoàn tất');
                } catch (error) {
                    AlertBox.Notify.Failure('Nhập dữ liệu thất bại');
                    console.log(error);
                }
            },
            changeFilter: function () {
                this.editForm.model.data.forEach(item => {
                    let match = false;
                    let fullname = (item.sv_ho + ' ' + item.sv_ten).trim().toLowerCase();
                    item.show = fullname.includes(this.filter.sv_hoten.trim().toLowerCase()) && item.sv_ma.trim().toLowerCase().includes(this.filter.sv_ma.trim().toLowerCase());
                })
            },
        },
    }
</script>
