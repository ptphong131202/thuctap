<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header" v-if="editForm.model.lh_ma">
            <h1 style="white-space: initial">
                Lớp {{ editForm.model.lh_ma }} - {{ editForm.model.lh_ten }}
                <small v-if="editForm.model.khoa_dao_tao">
                    Khóa đào tạo: {{ editForm.model.khoa_dao_tao.kdt_ten }}
                </small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active"><a :href="parent_url">Lớp học</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <!-- <section class="content"> -->
        <!-- <draggable class="content" v-model="editForm.listSemester" draggable=".semester-item" tag="section"> -->
        <section class="content">
            <div style="margin-bottom:6px;">
                <button type="button" class="btn bg-purple" @click="actionLuuHocKy">
                    <i class="fa fa-save"></i> Lưu
                </button>
                <button type="button" class="btn bg-green" @click="actionSyncMonHocTheoKhoaDaoTao">
                    <i class="fa fa-refresh"></i> Cập nhật thêm môn học
                </button>
                <a :href="parent_url" class="btn btn-default">
                    <i class="fa fa-share"></i> Trở về danh sách
                </a>
            </div>
            <div style="margin-bottom:6px;">
                <ul class="nav nav-tabs" style="cursor: pointer">
                    <li v-bind:class="{ active: tab === 0 }"><a @click="changeTab(0)">Sắp xếp học kỳ</a></li>
                    <li v-bind:class="{ active: tab === 1 }"><a @click="changeTab(1)">Sắp xếp môn học</a></li>
                </ul>
            </div>

            <div v-if="tab == 0" class="row semester-item">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-header with-border">
                            <!-- <div class="box-title">Học kỳ {{ semesterIndex + 1 }}</div> -->
                            <!-- <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="remove"
                                    v-if="semester.allow_delete && (semesterIndex > 0 || editForm.listSemester.length > 1)"
                                    @click="actionRemoveSemester(editForm.listSemester, semesterIndex)">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div> -->
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-hover table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="text-center w-3">#</th>
                                        <th>Mã môn học</th>
                                        <th>Môn học</th>
                                        <th class="w-10">Số tín chỉ</th>
                                        <th class="w-10">Số tiết/giờ</th>
                                        <th class="w-10">Tích lũy</th>
                                        <th class="w-10">Học kỳ</th>
                                        <th class="w-10">Thao tác</th>
                                    </tr>
                                </thead>
                                <!-- <draggable v-model="semester.monHoc" draggable=".drag-item" tag="tbody"> -->
                                <tbody>
                                    <tr v-for="(mh, semesterIndex) in editForm.listSemester" :key="semesterIndex">
                                        <td class="text-center">{{ semesterIndex + 1 }}</td>
                                        <td>{{ mh.mh_ma}}</td>
                                        <td>
                                            <!-- <select2 v-if="!mh.bang_diem_exists" v-model="mh.mh_id"
                                                :options="listMonHoc.select2" @change="actionSelectMonHoc(mh)"></select2> -->
                                            <span>{{ mh.mh_ten }}</span>
                                        </td>
                                        <td>{{ mh.mh_sodonvihoctrinh }}</td>
                                        <td>{{ mh.mh_sotiet }}</td>
                                        <td>{{ mh.mh_tichluy ? 'Có' : 'Không' }}</td>

                                        <select2 v-model="mh.pivot['lh_mh_hocky']" :options="listHocKy.select2"
                                            v-if="listMonHoc.select2.length > 0"
                                            @change="actionSelectHocKyChoMonHoc(mh, mh.pivot['lh_mh_hocky'])"></select2>
                                        <!-- <td><input type="checkbox" :checked="mh.pivot['lh_mh_hocky']" v-model="mh.pivot['lh_mh_hocky']" /></td> -->
                                        <!-- <td>{{ mh.mh_giangvien }}</td> -->

                                        <!-- <td class="text-center">
                                            <a href="javascript:void(0)"
                                                    v-if="!mh.bang_diem_exists && (monHocIndex > 0 || semester.monHoc.length > 1)"
                                                    @click="actionRemoveMonHoc(semester.monHoc, monHocIndex)">
                                                <i class="fa fa-trash text-red"></i>
                                            </a>
                                        </td> -->
                                        <td class="text-center">
                                            <!-- <a href="javascript:void(0)"
                                                    v-if="!mh.bang_diem_exists && (monHocIndex > 0 || mh.length > 1)"
                                                    @click="actionRemoveMonHoc(mh, semesterIndex)">
                                                <i class="fa fa-trash text-red"></i> 
                                            </a>-->
                                            <button class="btn btn-danger"
                                                @click="actionRemoveMonHoc(mh, semesterIndex)"><i
                                                    class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <!-- </draggable> -->
                            </table>
                        </div>
                        <!-- <div class="box-footer">
                            <button type="button" class="btn-link btn-sm" @click="actionAddNewMonHoc(semester)">
                                <i class="fa fa-plus"></i> Thêm dòng
                            </button>
                            <button type="button" class="btn-link btn-sm" @click="preActionThemMonHoc(semester)">
                                <i class="fa fa-plus-circle"></i> Thêm môn học mới
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>

            <div v-if="tab == 1" class="row semester-item" v-for="(semester, semesterIndex) in editForm.listSemester"
                :key="semesterIndex">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-header with-border">
                            <div class="box-title">Học kỳ {{ semesterIndex + 1 }}</div>
                            <!-- <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="remove"
                                    v-if="semester.allow_delete && (semesterIndex > 0 || editForm.listSemester.length > 1)"
                                    @click="actionRemoveSemester(editForm.listSemester, semesterIndex)">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div> -->
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-hover table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="text-center"></th>
                                        <th class="text-center w-3">#</th>
                                        <th>Môn học</th>
                                        <th class="w-10">Mã môn học</th>
                                        <th class="w-10">Số tín chỉ</th>
                                        <th class="w-10">Số tiết/giờ</th>
                                        <th class="w-10">Tích lũy</th>
                                        <!-- <th class="w-15">Giảng viên</th> -->
                                        <!-- <th class="text-center w-1">Xóa</th> -->
                                    </tr>
                                </thead>
                                <draggable v-model="semester.monHoc" draggable=".drag-item" tag="tbody">
                                    <tr v-for="(mh, monHocIndex) in semester.monHoc" :key="monHocIndex"
                                        class="drag-item">
                                        <td class="w-1 drag-area">
                                            <i class="fa fa-sort"></i>
                                        </td>
                                        <td class="text-center">{{ monHocIndex + 1 }}</td>
                                        <td>
                                            <span>{{ mh.mh_ten }}</span>
                                            <!-- <select2 v-if="!mh.bang_diem_exists" v-model="mh.mh_id"
                                                :options="listMonHoc.select2" @change="actionSelectMonHoc(mh)"></select2>
                                            <span v-if="mh.bang_diem_exists">{{ mh.mh_ten }}</span> -->
                                            
                                        </td>
                                        <td>{{ mh.mh_ma}}</td>
                                        <td>{{ mh.mh_sodonvihoctrinh }}</td>
                                        <td>{{ mh.mh_sotiet }}</td>
                                        <td>{{ mh.mh_tichluy ? 'Có' : 'Không' }}</td>
                                        <!-- <td>{{ mh.mh_giangvien }}</td> -->
                                        <!-- <td class="text-center">
                                            <a href="javascript:void(0)"
                                                v-if="!mh.bang_diem_exists && (monHocIndex > 0 || semester.monHoc.length > 1)"
                                                @click="actionRemoveMonHoc(semester.monHoc, monHocIndex)">
                                                <i class="fa fa-trash text-red"></i>
                                            </a>
                                        </td> -->
                                    </tr>
                                </draggable>
                            </table>
                        </div>
                        <!-- <div class="box-footer">
                            <button type="button" class="btn-link btn-sm" @click="actionAddNewMonHoc(semester)">
                                <i class="fa fa-plus"></i> Thêm dòng
                            </button>
                            <button type="button" class="btn-link btn-sm" @click="preActionThemMonHoc(semester)">
                                <i class="fa fa-plus-circle"></i> Thêm môn học mới
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>
            <!-- <button type="button" class="btn-link btn-sm" @click="actionAddNewSemester">
                <i class="fa fa-plus"></i> Thêm học kỳ
            </button> -->
            <button type="button" class="btn bg-purple" @click="actionLuuHocKy">
                <i class="fa fa-save"></i> Lưu
            </button>
            <a :href="parent_url" class="btn btn-default">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>
        </section>
        <!-- /.content -->

        <div class="modal fade" id="mon-hoc-edit-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Thêm môn học</h4>
                    </div>
                    <div class="modal-body">
                        <form-group :errors="monHocForm.errors" :field="'hdt_id'">
                            <label>Hệ đào tạo</label>
                            <select2 v-model="monHocForm.model.hdt_id" :options="listHeDaoTao.select2"></select2>
                        </form-group>
                        <form-group :errors="monHocForm.errors" :field="'mh_ma'">
                            <label>Mã môn học</label>
                            <input type="text" class="form-control" v-model="monHocForm.model.mh_ma" />
                        </form-group>
                        <form-group :errors="monHocForm.errors" :field="'mh_ten'">
                            <label>Tên môn học</label>
                            <input type="text" class="form-control" v-model="monHocForm.model.mh_ten" />
                        </form-group>
                        <form-group :errors="monHocForm.errors" :field="'mh_sodonvihoctrinh'">
                            <label>Số tín chỉ</label>
                            <input type="text" class="form-control" v-model="monHocForm.model.mh_sodonvihoctrinh" />
                        </form-group>
                        <form-group :errors="monHocForm.errors" :field="'mh_sotiet'">
                            <label>Số tiết/giờ</label>
                            <input type="text" class="form-control" v-model="monHocForm.model.mh_sotiet" />
                        </form-group>
                        <form-group :errors="monHocForm.errors" :field="'mh_tichluy'">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" v-model="monHocForm.model.mh_tichluy" /> Tính tích lũy
                                </label>
                            </div>
                        </form-group>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn bg-purple" v-on:click="actionThemMonHoc">Lưu</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="aync-monHoc-theoKhoaDaoTao-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Cập nhật thêm môn học thuộc chương trình đào tạo</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body table-responsive">
                            <table class="table table-hover table-striped table-bordered no-margin"
                                v-if="listMonHocSync.length > 0">
                                <thead>
                                    <tr>
                                        <th class="w-10"><input type="checkbox" v-model="checkAll"
                                                @change="toggleCheckAll" /> Chọn</th>
                                        <th class="text-center w-3">STT</th>
                                        <th>Môn học</th>
                                        <th class="w-10">Số tín chỉ</th>
                                        <th class="w-10">Số tiết/giờ</th>
                                        <th class="w-10">Tích lũy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(mh, semesterIndex) in listMonHocSync" :key="semesterIndex">
                                        <td><input type="checkbox" v-model="mh.mh_selected" /></td>
                                        <td class="text-center">{{ semesterIndex + 1 }}</td>
                                        <td>
                                            <span v-if="!mh.bang_diem_exists">{{ mh.mh_ten }}</span>
                                        </td>
                                        <td>{{ mh.mh_sodonvihoctrinh }}</td>
                                        <td>{{ mh.mh_sotiet }}</td>
                                        <td>{{ mh.mh_tichluy ? 'Có' : 'Không' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="alert alert-warning" role="alert" v-if="listMonHocSync.length == 0">
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Bạn đã thêm hết tất cả môn
                                học
                                thuộc chương trình đào tạo
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"
                            v-on:click="actionCloseUpdateSyncMonHocTheo">
                            <i class="fa fa-times-circle-o"></i> Đóng
                        </button>
                        <button type="button" class="btn bg-purple" v-if="listMonHocSync.length > 0"
                            v-on:click="actionUpdateSyncMonHocTheo(listMonHocSync)">
                            <i class="fa fa-save"></i> Cập nhật
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';

const monHocModal = {
    modal: function () {
        return $('#mon-hoc-edit-modal');
    },
    show: function () {
        this.modal().modal({ keyboard: false, backdrop: 'static' });
    },
    hide: function () {
        this.modal().modal('hide');
    }
}

const ayncMonHocModal = {
    modal: function () {
        return $('#aync-monHoc-theoKhoaDaoTao-modal');
    },
    show: function () {
        this.modal().modal({ keyboard: false, backdrop: 'static' });
    },
    hide: function () {
        this.modal().modal('hide');
    }
}

const consumer = {
    getLopHoc: function (lh_id) {
        const url = 'http://localhost/tthl/public/api/lop-hoc/' + lh_id;
        return axios.get(url)
            .then(response => response.data);
    },
    getDanhSachHocKy: function (lh_id) {
        const url = 'http://localhost/tthl/public/api/lop-hoc/' + lh_id + '/hoc-ky';
        return axios.get(url)
            .then(response => response.data)
            .then(data => {
                // Phân môn học ra từng học kỳ
                var semesters = [];
                data.forEach(function (item) {
                    let semesterIndex = item.pivot.lh_mh_hocky - 1;
                    if (semesters[semesterIndex] == undefined) {
                        semesters[semesterIndex] = {
                            allow_delete: false,
                            monHoc: []
                        };
                    }
                    semesters[semesterIndex].monHoc.push(item);
                });
                for (let i = 0; i < semesters.length; i++) {
                    if (semesters[i] == undefined) {
                        semesters[i] = {
                            allow_delete: false,
                            monHoc: [{}]
                        };
                    } else {
                        semesters[i].allow_delete = !semesters[i].monHoc.some(mh => mh.bang_diem_exists);
                    }
                }
                return semesters;
            })
            .then(data => {
                // Sắp xếp lại các môn học
                data.forEach(semester => {
                    semester.monHoc.sort((a, b) => a.pivot.lh_mh_index - b.pivot.lh_mh_index);
                });
                return data;
            });

    },
    getDanhSachHocKyTab0: function (lh_id) {
        const url = 'http://localhost/tthl/public/api/lop-hoc/' + lh_id + '/hoc-ky';
        return axios.get(url)
            .then(response => response.data)
            .then(data => {
                return data;
            });
    },
    getDanhSachMonHoc: function (kdt_id) {
        // const url = 'http://localhost/tthl/public/api/mon-hoc/all?hdt_id=' + hdt_id + '&nn_id=' + nn_id;
        // return axios.get(url)
        //         .then(response => response.data);
        const url = 'http://localhost/tthl/public/api/mon-hoc/all-by-khoa-dao-tao?kdt_id=' + kdt_id;
        return axios.get(url)
            .then(response => response.data);
    },
    getListHeDaoTao: function () {
        const url = 'http://localhost/tthl/public/api/he-dao-tao/all';
        return axios.get(url).then(response => response.data);
    },
    getMonHoc: function (mh_id) {
        const url = 'http://localhost/tthl/public/api/mon-hoc/' + mh_id;
        return axios.get(url)
            .then(response => response.data);
    },
    saveOrUpdateMonHoc: function (formData) {
        if (formData.mh_id == null) {
            return axios.post('http://localhost/tthl/public/api/mon-hoc', formData);
        } else {
            return axios.put('http://localhost/tthl/public/api/mon-hoc/' + formData.mh_id, formData);
        }
    },
    saveOrUpdateHocKy: function (lh_id, danhSachHocKy) {
        return axios.post(`/api/lop-hoc/${lh_id}/hoc-ky`, { semesters: danhSachHocKy });
    },
    saveOrUpdateHocKyTab0: function (lh_id, danhSachHocKy) {
        return axios.post(`/api/lop-hoc/${lh_id}/hoc-ky-tab0`, { semesters: danhSachHocKy });
    },
    getSyncMonHocTheoKhoaDaoTao: function (data) {
        return axios.post(`/api/lop-hoc/get-sync-mon-hoc-by-kdt`, data);
    },
    updateSyncMonHocTheoKhoaDaoTao: function (data, lh_id) {
        return axios.put(`/api/lop-hoc/${lh_id}/update-sync-mon-hoc-by-kdt`, data);
    },
    deleteMonHoc: function (mh_id) {
        return axios.delete(`/api/lop-hoc/delete_mh/${mh_id}`);
    }
}

export default {
    props: ['lh_id', 'parent_url'],
    mounted() {
        this.init();
    },
    updated() {
        this.loadHocKyTheoHeDaoTao();

    },
    data() {
        return {
            current: {
                semester: Object
            },
            monHocForm: {
                model: Object,
                errors: Object
            },
            editForm: {
                model: Object,
                listSemester: [],
                errors: Object
            },
            listMonHoc: {
                original: [],
                select2: [],
            },
            listHeDaoTao: {
                select2: [],
            },
            tab: 0,
            listHocKy: {
                select2: [],
            },
            hdt_id: 0,
            soHocKy: 0,
            checkAll: false,
            listMonHocSync: []
        }
    },
    methods: {
        toggleCheckAll() {
            this.listMonHocSync.forEach(mh => {
                mh.mh_selected = this.checkAll;
            });
        },
        loadDanhSachMonHoc: function (kdt_id) {
            consumer.getDanhSachMonHoc(kdt_id).then(data => {
                this.listMonHoc.original = data;
                this.listMonHoc.select2 = data.map(item => {
                    return {
                        id: item.mh_id,
                        text: item.mh_ma + ' - ' + item.mh_ten
                    };
                })
            })
        },
        loadHocKyTheoHeDaoTao: function () {
            var vm = this;
            if (vm.listHocKy.select2.length == 0) {
                // Cao đẳng
                if (vm.hdt_id == 4) {
                    this.soHocKy = 6;
                    vm.listHocKy.select2.push({ id: 0, text: "Chọn học kỳ" });
                    for (let i = 1; i < 7; i++) {
                        vm.listHocKy.select2.push({ id: i, text: `Học kỳ ${i}` });
                    }
                } else {
                    // Trung cấp
                    this.soHocKy = 4;
                    vm.listHocKy.select2.push({ id: 0, text: "Chọn học kỳ" });
                    for (let i = 1; i < 5; i++) {
                        vm.listHocKy.select2.push({ id: i, text: `Học kỳ ${i}` });
                    }
                }
            }
        },
        loadLopHoc: function () {
            var vm = this;
            consumer.getLopHoc(this.lh_id).then(data => {
                this.editForm.model = data;
                this.hdt_id = data.khoa_dao_tao.hdt_id;

                // let hdt_id = data.khoa_dao_tao.hdt_id;
                // let nn_id = data.khoa_dao_tao.nn_id;
                this.loadDanhSachMonHoc(data.kdt_id);
            });

            if (this.editForm.listSemester) {
                if (this.tab == 0) {
                    consumer.getDanhSachHocKyTab0(vm.lh_id)
                        .then(semesters => {
                            if (semesters == 0) {
                                let newNemester = vm.createNewSemesterObject();
                                vm.editForm.listSemester.push(newNemester);
                            } else {
                                vm.editForm.listSemester = semesters;
                            }
                        });

                } else {
                    consumer.getDanhSachHocKy(vm.lh_id)
                        .then(semesters => {
                            if (semesters == 0) {
                                let newNemester = vm.createNewSemesterObject();
                                vm.editForm.listSemester.push(newNemester);
                            } else {
                                vm.editForm.listSemester = semesters;
                            }
                        });
                }
            }

        },

        init: function () {
            this.loadLopHoc();
        },
        createNewSemesterObject: function () {
            return {
                allow_delete: true,
                monHoc: [{}]
            };
        },
        actionAddNewSemester: function () {
            if (this.editForm.listSemester.length < 6) {
                let newNemester = this.createNewSemesterObject();
                this.editForm.listSemester.push(newNemester);
            } else {
                AlertBox.Notify.Failure('Không thể thêm hơn 6 quá học kỳ');
            }
        },
        actionRemoveSemester: function (list, index) {
            if (list.length > 1) {
                list.splice(index, 1);
            }
        },
        actionAddNewMonHoc: function (semester) {
            semester.monHoc.push({});
        },
        actionSelectMonHoc: function (monHoc) {
            var found = this.listMonHoc.original.find(item => item.mh_id == monHoc.mh_id)
            if (found) {
                Object.assign(monHoc, found);
            }
        },
        actionSelectHocKyChoMonHoc: function (monHoc, lh_mh_hocky) {
            var found = this.editForm.listSemester.find(item => item.mh_id == monHoc.mh_id)
            const lhMhJocky = parseInt(lh_mh_hocky);
            if (found) {
                found.pivot = {
                    lh_mh_hocky: lhMhJocky,
                    lh_mh_index: 0
                };
            };
        },
        actionRemoveMonHoc: function (list, index) {
            if (this.editForm.listSemester.length > 0) {
                if (confirm(`Bạn có chắc muốn xóa môn học "${list.mh_ten}" không?`)) {
                    consumer.deleteMonHoc(list.mh_id)
                        .then(response => {
                            AlertBox.Notify.Success(`Xóa môn học "${list.mh_ten}" thành công`);
                            this.editForm.listSemester.splice(index, 1);
                            // this.reloadList();
                        })
                        .catch(error => {
                            AlertBox.Notify.Error(`Xóa môn học "${list.mh_ten}" thất bại`);
                        });
                }

            }
        },
        resetEditFormMonHoc: function () {
            this.monHocForm = {
                model: {
                    hdt_id: 0,
                    nn_id: this.editForm.model.nn_id,
                    mh_tichluy: true,
                }
            };
            Vue.set(this.monHocForm, 'errors', {});
        },
        preActionThemMonHoc: function (semester) {
            this.current.semester = semester;
            if (this.listHeDaoTao.select2.length == 0) {
                consumer.getListHeDaoTao().then(data => {
                    this.listHeDaoTao.select2 = data.map(item => {
                        return {
                            id: item.hdt_id,
                            text: item.hdt_ten
                        };
                    });
                    this.listHeDaoTao.select2 = [{ id: 0, text: 'Dùng chung' }, ...this.listHeDaoTao.select2];

                    this.listHeDaoTao.select2 = this.listHeDaoTao.select2.filter(item => item.id == 0 || item.id == this.editForm.model.hdt_id);

                    this.resetEditFormMonHoc();
                    monHocModal.show();
                });
            } else {
                this.resetEditFormMonHoc();
                monHocModal.show();
            }
        },
        actionThemMonHoc: function () {
            var vm = this;
            this.monHocForm.model.nn_id = this.editForm.model.nn_id;
            consumer.saveOrUpdateMonHoc(this.monHocForm.model)
                .then(response => {
                    if (response.status == 200) {
                        vm.resetEditFormMonHoc();
                        vm.loadDanhSachMonHoc(vm.editForm.model.hdt_id, vm.editForm.model.nn_id);
                        vm.current.semester.monHoc.push(response.data);
                        monHocModal.hide();
                        AlertBox.Notify.Success('Thêm thành công');
                    }
                })
                .catch(error => {
                    if (error.response.status == 422) {
                        Vue.set(vm.monHocForm, 'errors', error.response.data.errors);
                    }
                });
        },
        actionSyncMonHocTheoKhoaDaoTao: function () {
            let requestData = {
                'lh_id': this.lh_id,
                'dsMh_lh_current': this.editForm.listSemester
            };

            consumer.getSyncMonHocTheoKhoaDaoTao(requestData)
                .then(response => {
                    if (response.status == 200) {
                        this.listMonHocSync = response.data;
                        ayncMonHocModal.show();
                    }
                })
                .catch(error => {
                    AlertBox.Notify.Error('Lấy thông tin môn hoc thất bại!');
                });
        },
        actionUpdateSyncMonHocTheo: function (listMonHocSync) {
            const lophoc_id = this.lh_id;

            // Lọc các phần tử có thuộc tính mh_selected là true và thuộc tính tồn tại
            listMonHocSync = listMonHocSync.filter(function (item) {
                return item.mh_selected === true && item.hasOwnProperty('mh_selected');
            });

            const listMonHocSyncReq = {
                listMonHocSync: listMonHocSync,
                soHocKy: this.soHocKy
            };

            consumer.updateSyncMonHocTheoKhoaDaoTao(listMonHocSyncReq, lophoc_id)
                .then(response => {
                    if (response.status == 200) {
                        this.loadLopHoc();
                        AlertBox.Notify.Success('Cập nhật thêm môn học thành công');
                        ayncMonHocModal.hide();
                    }
                })
                .catch(error => {
                    AlertBox.Notify.Error('Cập nhật thêm môn học thất bại!');
                });

                actionCloseUpdateSyncMonHocTheo();
        },
        actionCloseUpdateSyncMonHocTheo: function () {
            this.checkAll = false;
        },
        actionLuuHocKy: function () {
            if (this.tab == 0) {
                this.actionLuuHocKyTab0();
            } else {
                this.actionLuuHocKyTab1();
            }
        },
        actionLuuHocKyTab1: function () {
            var vm = this;

            let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) != index)
            var passValidate = true;
            var semesters = this.editForm.listSemester.map(function (semester, semesterIndex) {
                let listMonHocId = semester.monHoc
                    .filter(function (monHoc) {
                        return monHoc.mh_id != undefined;
                    })
                    .map(function (monHoc) {
                        return monHoc.mh_id;
                    });
                if (passValidate) {
                    passValidate = findDuplicates(listMonHocId).length == 0;
                    if (!passValidate) {
                        AlertBox.Notify.Failure('Học kỳ ' + (semesterIndex + 1) + ' có 2 môn trùng nhau');
                    }
                }

                return {
                    'lh_mh_hocky': semesterIndex + 1,
                    'mh_ids': listMonHocId
                }
            });


            if (passValidate) {
                consumer.saveOrUpdateHocKy(this.editForm.model.lh_id, semesters)
                    .then(response => {
                        if (response.status == 200) {
                            AlertBox.Notify.Success('Lưu thành công');
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        }
                    });
            }
        },
        actionLuuHocKyTab0: function () {
            var vm = this;


            let listMonHocId = this.editForm.listSemester
                .filter(function (monHoc) {
                    return monHoc.mh_id != undefined;
                })
                .map(function (monHoc) {
                    return monHoc.mh_id;
                });

            let listLh_mh_hocky = this.editForm.listSemester
                .filter(function (monHoc) {
                    return monHoc.pivot["lh_mh_hocky"] != undefined ?? 1;
                })
                .map(function (monHoc) {
                    return monHoc.pivot["lh_mh_hocky"];
                });

            let lh_mh_index = this.editForm.listSemester
                .filter(function (monHoc) {
                    return monHoc.pivot["lh_mh_index"] != undefined ?? 0;
                })
                .map(function (monHoc) {
                    return monHoc.pivot["lh_mh_index"];
                });

            let semesters = {
                'mh_ids': listMonHocId,
                'lh_mh_hocky': listLh_mh_hocky,
                'lh_mh_index': lh_mh_index
            }

            // console.log("listLh_mh_hocky",listLh_mh_hocky)

            consumer.saveOrUpdateHocKyTab0(this.lh_id, semesters)
                .then(response => {
                    if (response.status == 200) {
                        AlertBox.Notify.Success('Lưu thành công');
                    }
                })
                .catch(error => {
                    if (error.response.status == 422) {
                        Vue.set(vm.editForm, 'errors', error.response.data.errors);
                    }
                });
        },
        changeTab: function (tab) {
            this.tab = tab;
            this.loadLopHoc();
        }
    }
}
</script>
