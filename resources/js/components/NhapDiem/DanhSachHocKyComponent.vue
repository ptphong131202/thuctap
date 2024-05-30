<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Danh sách môn học lớp {{ model.lh_ma }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Danh sách môn học</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a :href="parent_url" class="btn btn-default" style="margin-bottom:6px;">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <select class="form-control" v-model="filter.semester" v-on:change="semesterChange">
                            <option value="123456">Tất cả môn học</option>
                            <option value="1">Môn học học kỳ 1 năm I</option>
                            <option value="2">Môn học học kỳ 2 năm I</option>
                            <option value="3">Môn học học kỳ 1 năm II</option>
                            <option value="4">Môn học học kỳ 2 năm II</option>
                            <option value="5">Môn học học kỳ 1 năm III</option>
                            <option value="6">Môn học học kỳ 2 năm III</option>
                            <option value="-1">Môn học theo đợt thi</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" v-for="(semester, sIndex) in table.list" :key="sIndex">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-header with-border">
                            <div class="box-title">Học kỳ {{ semester.semesterByYear }} năm {{ semester.year }}</div>
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-hover table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="text-center w-3">STT</th>
                                        <th class="w-10">Mã môn học</th>
                                        <th>Tên môn học</th>
                                        <th class="w-10 text-center">Số tín chỉ</th>
                                        <th class="w-10 text-center">Ngày nhập điểm</th>
                                        <th class="w-10 text-center">Giảng viên</th>
                                        <th class="w-7 text-center" style="min-width: 150px">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(mh, mIndex) in semester.monHoc" :key="mIndex">
                                        <td class="text-center">{{ mIndex + 1 }}</td>
                                        <td>
                                            <span>{{ mh.mh_ma }}</span>
                                        </td>
                                        <td>
                                            <span>{{ mh.mh_ten }}</span>
                                        </td>
                                        <td class="text-center">{{ mh.mh_sodonvihoctrinh }}</td>
                                        <td class="text-center">
                                            <span :title="mh.bd_created_at | moment">{{ mh.bd_created_at | moment }}</span>
                                        </td>
                                        <td>
                                            {{ mh.bd_giangvien }}
                                        </td>
                                        <td>
                                            <a :href="{ ...mh, semester: sIndex + 1, lh_id: lh_id } | nhapDiemMonHocUrl"
                                                class="btn bg-purple btn-sm" v-if="quyenNhapDiem"
                                                :title="'Nhập điểm môn ' + mh.mh_ten">
                                                <i class="fa fa-pencil"></i> Nhập điểm
                                            </a>
                                            <button type="button" title="Thông báo điểm" 
                                            class="btn btn-warning btn-sm"  v-on:click="shareNotification(mh, model)">
                                            <i class="fa-solid fa-share"></i>
                                        </button>
                                        <button type="button" title="Xóa điểm đã nhập" v-if="mh.bd_id && quyenNhapDiem"
                                            class="btn btn-danger btn-sm" style="margin-top: 5px;" v-on:click="destroyBangDiem(mh.bd_id)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">{{ semester.monHoc.length + 1 }}</td>
                                        <td colspan="3">Điểm rèn luyện học kỳ {{ semester.semesterByYear }} năm {{
                                            semester.year }}</td>
                                        <td class="text-center">
                                            <span v-if="semester.diemRenLuyen">{{ semester.diemRenLuyen.created_at | moment
                                            }}</span>
                                        </td>
                                        <td></td>
                                        <td>
                                            <a :href="{ semester: sIndex + 1, lh_id: lh_id } | nhapDiemRenLuyenUrl"
                                                class="btn bg-purple btn-sm" v-if="quyenNhapRenLuyen"
                                                :title="'Nhập điểm rèn luyện học kỳ ' + (sIndex + 1)">
                                                <i class="fa fa-pencil"></i> Nhập điểm
                                            </a>
                                            <button type="button" title="Xóa điểm đã nhập"
                                                v-if="semester.diemRenLuyen && quyenNhapRenLuyen"
                                                class="btn btn-danger btn-sm"
                                                v-on:click="destroyBangDiem(semester.diemRenLuyen.bd_id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer">
                            <a :href="'/tthl11/public/nhap-diem/' + lh_id + '/ket-qua-hoc-tap?semester=' + (sIndex + 1)"
                                :title="'Xem kết quả học lập học kỳ ' + (sIndex + 1)" class="btn-link btn-sm">
                                <i class="fa fa-eye"></i> Xem kết quả học tập
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <a :href="parent_url" class="btn btn-default">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>
        </section>
        <!-- /.content -->
    </div>
</template>

<script>
const consumer = {
    getDanhSachHocKy: function (lh_id, semester) {
        const url = 'http://localhost/tthl11/public/api/nhap-diem/' + lh_id + '/hoc-ky?semester=' + semester;
        return axios.get(url)
            .then(response => response.data)
            .then(data => {
                // Phân môn học ra từng học kỳ
                var semesters = [];
                data.danh_sach_mon_hoc.forEach(function (item) {
                    let semesterIndex = item.kdt_mh_hocky - 1;
                    if (semesters[semesterIndex] == undefined) {
                        let semesterByYear = +item.kdt_mh_hocky + 2 - (Math.round((item.kdt_mh_hocky) / 2) * 2);
                        let year = Math.round((item.kdt_mh_hocky) / 2);
                        semesters[semesterIndex] = {
                            allow_delete: true,
                            semester: item.kdt_mh_hocky,
                            semesterByYear: semesterByYear,
                            year: year,
                            monHoc: []
                        };
                    }
                    semesters[semesterIndex].monHoc.push(item);
                });
                for (let i = 0; i < semesters.length; i++) {
                    if (semesters[i] == undefined) {
                        semesters[i] = {
                            monHoc: [{}]
                        };
                    }
                    semesters[i].diemRenLuyen = data.danh_sach_diem_ren_luyen.find(item => item.kdt_hocky == (i + 1));
                }
                if (semester == 123456) {
                    return { dotthi: data.danh_sach_dot_thi, semesters: semesters };
                } else if (semester == -1) {
                    return { dotthi: data.danh_sach_dot_thi, semesters: [] };
                } else if (semesters[semester - 1] != undefined) {
                    return { dotthi: data.danh_sach_dot_thi, semesters: [semesters[semester - 1]] };
                } else {
                    return [];
                }

            })
            .then(data => {
                // Sắp xếp lại các môn học
                data.semesters.forEach(semester => {
                    semester.monHoc.sort((a, b) => a.kdt_mh_index - b.kdt_mh_index);
                });
                var rs = {};
                rs.semester = data.semesters;
                rs.dotthi = data.dotthi;
                return rs;
            });
    },
    getLopHoc: function (lh_id) {
        const url = 'http://localhost/tthl11/public/api/lop-hoc/' + lh_id;
        return axios.get(url).then(response => response.data);
    },
    destroyBangDiem: function (bd_id) {
        const url = `http://localhost/tthl11/public/api/nhap-diem/${bd_id}/bang-diem`;
        return axios.delete(url);
    },
    destroyBangDiemDotThi: function (dt_bd_id) {
        const url = `http://localhost/tthl11/public/api/nhap-diem/${dt_bd_id}/bang-diem-dot-thi`;
        return axios.delete(url);
    },
}

const store = {
    table: {
        set list(data) {
            window.localStorage.setItem('nhap-diem.hoc-ky.table.list', JSON.stringify(data));
        },
        get list() {
            return window.localStorage.getItem('nhap-diem.hoc-ky.table.list') ? JSON.parse(window.localStorage.getItem('nhap-diem.hoc-ky.table.list')) : {};
        }
    }
}

export default {
    props: ['lh_id', 'permissions', 'parent_url'],
    mounted() {
        this.quyenNhapDiem = this.permissions.includes('admin.score');
        this.quyenNhapRenLuyen = this.permissions.includes('nhaprenluyen');
        this.init(this.lh_id);
    },
    data() {
        return {
            quyenNhapDiem: false,
            quyenNhapRenLuyen: false,
            filter: {
                semester: 123456,
            },
            model: Object,
            table: {
                list: {},
            },
            tableDotThi: {
                list: {},
            }
        }
    },
    filters: {
        moment: function (date) {
            if (date) {
                return moment(date).format('HH:mm DD/MM/yyyy');
            }
            return null;
        },
        nhapDiemMonHocUrl: function (monHoc) {
            let baseSixFour = btoa(JSON.stringify({ lh_id: monHoc.lh_id, hoc_ky: monHoc.semester, mh_id: monHoc.mh_id }));
            return 'http://localhost/tthl11/public/api/nhap-diem/mon-hoc/' + baseSixFour;
        },
        nhapDiemDotThiUrl: function (monHoc) {
            let baseSixFour = btoa(JSON.stringify({ lh_id: monHoc.lh_id, dot_thi: monHoc.dotthi, mh_id: monHoc.mh_id }));
            return 'http://localhost/tthl11/public/api/nhap-diem/dot-thi/' + baseSixFour;
        },
        nhapDiemRenLuyenUrl: function (semester) {
            let baseSixFour = btoa(JSON.stringify({ lh_id: semester.lh_id, hoc_ky: semester.semester }));
            return 'http://localhost/tthl11/public/api/nhap-diem/ren-luyen/' + baseSixFour;
        }
    },
    methods: {
        reloadList: function (lh_id, semester) {
            var vm = this;
            consumer.getDanhSachHocKy(lh_id, semester).then(data => {
                Vue.set(vm.table, 'list', data.semester);
                store.table.list = data.semester;

                Vue.set(vm.tableDotThi, 'list', data.dotthi);
                store.table.list = data.dotthi;
            });
        },
        init: function (lh_id) {
            consumer.getLopHoc(lh_id).then(data => {
                this.model = data;
            });
            this.reloadList(lh_id, this.filter.semester);
        },
        semesterChange: function () {
            this.init(this.lh_id);
        },
        shareNotification: function(mh, model) {
            alert(`Chia sẻ điểm môn ${mh.mh_id  }, lớp ${model.lh_id}`);
            
            /* const url = `http://localhost/tthl11/public/api/nhap-diem/`;
            return axios.post(url, { mh_id: mh.mh_id, lh_ma: model.lh_ma })
                .then(response => {
            // Hiển thị thông tin chi tiết trả về trong alert
            alert('Thông báo đã được gửi đi thành công!\n' + JSON.stringify(response.data, null, 2));
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gửi thông báo thất bại!');
                }); */
        },
        destroyBangDiem: function (bd_id) {
            var vm = this;
            AlertBox.Comfirm('Xác nhận',
                'Bạn có đồng ý xóa',
                function () {
                    consumer.destroyBangDiem(bd_id).then(response => {
                        vm.reloadList(vm.lh_id, vm.filter.semester);
                        AlertBox.Notify.Success('Xóa thành công');
                    })
                },
                function () {

                })
        },
        destroyBangDiemDotThi: function (dt_bd_id) {
            var vm = this;
            AlertBox.Comfirm('Xác nhận',
                'Bạn có đồng ý xóa',
                function () {
                    consumer.destroyBangDiemDotThi(dt_bd_id).then(response => {
                        vm.reloadList(vm.lh_id, vm.filter.semester);
                        AlertBox.Notify.Success('Xóa thành công');
                    })
                },
                function () {

                })
        }

    }
}
</script>



<!-- SELECT sv.sv_sdt, COUNT(*)
SELECT sv.*
FROM qlsv_lophoc AS lh
JOIN qlsv_lophoc_monhoc AS lh_mh ON lh.lh_id = lh_mh.lh_id
JOIN qlsv_sinhvien_lophoc AS lhs ON lh.lh_id = lhs.lh_id
JOIN qlsv_sinhvien AS sv ON lhs.sv_id = sv.sv_id
WHERE lh.lh_ma = 'C-NTS15A' AND lh_mh.mh_id = 1518;
 -->