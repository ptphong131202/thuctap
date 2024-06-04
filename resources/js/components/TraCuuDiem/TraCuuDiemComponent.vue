<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Nhập điểm
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Lớp học</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                    <h1>KẾT QUẢ ĐÁNH GIÁ GIÁ MÔ ĐUN</h1>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Lớp</label>
                                        <div style="font-size: 24px" v-if="editForm.lopHoc.lh_ma">
                                            <b>{{ editForm.lopHoc.lh_ma }}</b> - {{ editForm.lopHoc.lh_ten }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Niên khóa</label>
                                        <div style="font-size: 24px" v-if="editForm.lopHoc.lh_ma">
                                            {{ editForm.lopHoc.nien_khoa.nk_ten }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Học kỳ</label>
                                        <select class="form-control" v-model="editForm.model.kdt_hocky" @change="actionChangeHocKy">
                                            <option value="-1">--- Chọn học kỳ ---</option>
                                            <option v-for="(hocKy, hkIndex) in listSemester" :key="hkIndex" :value="hkIndex + 1">Học kỳ {{ hkIndex + 1 }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Tên mô đun</label>
                                        <select class="form-control" v-model="editForm.model.mh_id" @change="actionChangeMonHoc">
                                            <option value="-1">--- Chọn mô đun ---</option>
                                            <option v-for="(monHoc, mhIndex) in listMonHoc" :key="mhIndex" :value="monHoc.mh_id">{{ monHoc.mh_ma }} - {{ monHoc.mh_ten }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Thời gian thực hiện từ ngày</label>
                                        <input type="date" class="form-control" v-model="editForm.model.bd_tungay"/>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>đến ngày</label>
                                        <input type="date" class="form-control" v-model="editForm.model.bd_denngay"/>
                                    </div>
                                </div>
                            </div>
                            <div id="panel-diem">
                                <table class="table table-hover table-striped table-bordered no-margin">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="w-3 text-center">#</th>
                                            <th rowspan="2" class="text-center w-5">MSSV</th>
                                            <th rowspan="2" colspan="2" class="text-center">Họ và tên</th>
                                            <th rowspan="2" class="w-10 text-center" style="min-width: 85px">Dự lớp (%)</th>
                                            <th colspan="2" class="w-20 text-center" style="min-width: 170px">Điểm tổng kết</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Lần 1</th>
                                            <th class="text-center">Lần 2</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(sv, indexSv) in editForm.model.data" :key="indexSv">
                                            <td class="text-center">{{ indexSv + 1 }}</td>
                                            <td class="text-center">{{ sv.sv_ma }}</td>
                                            <td class="w-10">{{ sv.sv_ho }}</td>
                                            <td class="w-10">{{ sv.sv_ten }}</td>
                                            <td>
                                                <input type="number" v-model="sv.svd_dulop" class="form-control"/>
                                            </td>
                                            <td>
                                                <input type="number" v-model="sv.svd_first" class="form-control"/>
                                            </td>
                                            <td>
                                                <input type="number" v-model="sv.svd_second" class="form-control"/>
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
                            <button type="button" class="btn bg-purple" v-on:click="actionSave">
                                <i class="fa fa-save"></i> Lưu
                            </button>
                            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-default">
                                <i class="fa fa-share"></i> Trở về danh sách
                            </a>
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
            const url = 'http://localhost/cea_4.0/public/api/lop-hoc/' + lh_id;
            return axios.get(url).then(response => response.data);
        },
        getDanhSachHocKy: function (kdt_id) {
            const url = 'http://localhost/cea_4.0/public/api/khoa-dao-tao/' + kdt_id + '/hoc-ky';
            return axios.get(url)
                .then(response => response.data)
                .then(data => {
                    // Phân môn học ra từng học kỳ
                    var semesters = [];
                    data.forEach(function (item) {
                        let semesterIndex = item.pivot.kdt_mh_hocky - 1;
                        if (semesters[semesterIndex] == undefined) {
                            semesters[semesterIndex] = {
                                monHoc: []
                            };
                        }
                        semesters[semesterIndex].monHoc.push(item);
                    });
                    for (let i = 0; i < semesters.length; i++) {
                        if (semesters[i] == undefined) {
                            semesters[i] = {
                                monHoc: [ ]
                            };
                        }
                    }

                    return semesters;
                })
                .then(data => {
                    // Sắp xếp lại các môn học
                    data.forEach(semester => {
                        semester.monHoc.sort((a, b) => a.pivot.kdt_mh_index - b.pivot.kdt_mh_index);
                    });
                    return data;
                });
        },
        getBangDiem: function(lh_id, mh_id, hocKy) {
            const url = `http://localhost/cea_4.0/public/api/nhap-diem/${lh_id}/bang-diem?mh_id=${mh_id}&hocky=${hocKy}`;
            return axios.get(url).then(response => response.data);
        },
        saveBangDiem: function (formData) {
            const url = `http://localhost/cea_4.0/public/api/nhap-diem/${formData.lh_id}/bang-diem`;
            return axios.post(url, formData);
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
        props: ['lh_id'],
        mounted() {
            this.loadLopHoc();
        },
        data() {
            return {
                editForm: {
                    lopHoc: Object,
                    model: {
                        kdt_hocky: -1,
                        mh_id: -1,
                        lh_id: -1,
                        data: [],
                    },
                },
                listSemester: [],
                listMonHoc: [],
            }
        },
        methods: {
            loadLopHoc: function () {
                var vm = this;
                // Load thông tin lớp học
                consumer.getLopHoc(this.lh_id).then(lopHoc => {
                    this.editForm.lopHoc = lopHoc;

                    // Load danh sách học kỳ
                    consumer.getDanhSachHocKy(this.editForm.lopHoc.kdt_id)
                        .then(semesters => {
                            vm.listSemester = semesters;
                            if (semesters) {
                                vm.editForm.model.kdt_hocky = 1;
                                vm.listMonHoc = semesters[vm.editForm.model.kdt_hocky - 1].monHoc;
                                if (vm.listMonHoc) {
                                    vm.editForm.model.mh_id = vm.listMonHoc[0].mh_id;
                                    // Load danh sinh viên và bảng điểm của môn
                                    this.reloadSinhVien();
                                }
                            }
                        });
                });
            },
            reloadSinhVien: function () {
                let lh_id = this.lh_id;
                let mh_id = this.editForm.model.mh_id;
                let hocKy = this.editForm.model.kdt_hocky;
                panelDiem.block();
                consumer.getBangDiem(lh_id, mh_id, hocKy)
                    .then(data => {
                         this.editForm.model = data;
                         panelDiem.unblock();
                    });
            },
            actionChangeHocKy: function () {
                if (this.editForm.model.kdt_hocky != -1) {
                    var semester = this.listSemester[this.editForm.model.kdt_hocky - 1];
                    this.listMonHoc = semester.monHoc;
                    if (this.listMonHoc.length > 0) {
                        this.editForm.model.mh_id = this.listMonHoc[0].mh_id;
                        this.reloadSinhVien();
                    } else {
                        this.editForm.model = {
                            mh_id: -1,
                            data: []
                        }
                    }
                } else {
                    this.editForm.model = {
                        lh_id: this.lh_id,
                        mh_id: -1,
                        kdt_hocky: -1,
                        data: []
                    }
                }
            },
            actionChangeMonHoc: function () {
                if (this.editForm.model.mh_id != -1) {
                    this.reloadSinhVien();
                } else {
                    this.editForm.model.mh_id = -1;
                    this.editForm.model.data = [];
                }
            },
            actionSave: function () {
                var data = {
                    lh_id: this.lh_id,
                    mh_id: this.editForm.model.mh_id,
                    kdt_hocky: this.editForm.model.kdt_hocky,
                    bd_tungay: this.editForm.model.bd_tungay,
                    bd_denngay: this.editForm.model.bd_denngay,
                    data: this.editForm.model.data,
                }

                consumer.saveBangDiem(data).then(response => {
                        AlertBox.Notify.Success('Lưu thành công');
                    })
                    .catch(error => {
                        AlertBox.Notify.Failure('Cập nhật thất bại');
                    });
            }
        },
    }
</script>
