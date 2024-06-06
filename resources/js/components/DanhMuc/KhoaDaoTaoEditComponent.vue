<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                {{ editForm.model.kdt_ten }}
                <small v-if="editForm.model.he_dao_tao">
                    Hệ {{ editForm.model.he_dao_tao.hdt_ten }}
                </small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Chương trình đào tạo</li>
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
                <button type="button" class="btn bg-green" @click="actionSyncMonHocTheoNganhNghe">
                    <i class="fa fa-refresh"></i> Cập nhật thêm môn học
                </button>
                <a :href="parent_url" class="btn btn-default">
                    <i class="fa fa-share"></i> Trở về danh sách
                </a>
            </div>
            <div class="row semester-item">
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
                            <table class="table table-hover table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <!-- <th class="text-center"></th> -->
                                        <th class="text-center w-3">STT</th>
                                        <th>Môn học</th>
                                        <th class="w-10">Số tín chỉ</th>
                                        <th class="w-10">Số tiết/giờ</th>
                                        <th class="w-10">Tích lũy</th>
                                        <!-- <th class="w-15">Giảng viên</th> -->
                                        <th class="w-10">Áp dụng</th>
                                        <!-- <th class="text-center w-1">Xóa</th> -->
                                    </tr>
                                </thead>
                                <!-- <draggable v-model="semester.monHoc" draggable=".drag-item" tag="tbody"> -->
                                <tbody>
                                    <tr v-for="(mh, semesterIndex) in editForm.listSemester" :key="semesterIndex">
                                        <!-- <td class="w-1 drag-area">
                                            <i class="fa fa-sort"></i>
                                        </td> -->
                                        <td class="text-center">{{ semesterIndex + 1 }}</td>
                                        <td>
                                            <!-- <select2 v-if="!mh.bang_diem_exists" v-model="mh.mh_id"
                                                :options="listMonHoc.select2" @change="actionSelectMonHoc(mh)"></select2> -->
                                            <span v-if="!mh.bang_diem_exists">{{ mh.mh_ten }}</span>
                                        </td>
                                        <td>{{ mh.mh_sodonvihoctrinh }}</td>
                                        <td>{{ mh.mh_sotiet }}</td>
                                        <td>{{ mh.mh_tichluy ? 'Có' : 'Không' }}</td>
                                        <!-- <td>{{ mh.mh_giangvien }}</td> -->
                                        <td><input type="checkbox" :checked="mh.pivot['kdt_mh_apdung']"
                                                v-model="mh.pivot['kdt_mh_apdung']" />
                                        </td>
                                        <!-- <td class="text-center">
                                            <a href="javascript:void(0)"
                                                    v-if="!mh.bang_diem_exists && (monHocIndex > 0 || semester.monHoc.length > 1)"
                                                    @click="actionRemoveMonHoc(semester.monHoc, monHocIndex)">
                                                <i class="fa fa-trash text-red"></i>
                                            </a>
                                        </td> -->
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


        <div class="modal fade" id="aync-monHoc-theoNganhNghe-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Cập nhật thêm môn học thuộc ngành nghề đào tạo</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body table-responsive">
                            <table class="table table-hover table-striped table-bordered no-margin"
                                v-if="listMonHocSync.length > 0">
                                <thead>
                                    <tr>
                                        <th class="w-10">Chọn</th>
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
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Bạn đã thêm hết tất cả môn học
                                thuộc ngành nghề đào tạo
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fa fa-times-circle-o"></i> Đóng
                        </button>
                        <button type="button" class="btn bg-purple" v-if="listMonHocSync.length > 0"
                            v-on:click="actionUpdateSyncMonHocTheoNganhNghe(listMonHocSync)">
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
        return $('#aync-monHoc-theoNganhNghe-modal');
    },
    show: function () {
        this.modal().modal({ keyboard: false, backdrop: 'static' });
    },
    hide: function () {
        this.modal().modal('hide');
    }
}

const consumer = {
    getKhoaDaotao: function (kdt_id) {
        const url = 'http://localhost/cea-2.1/public/api/khoa-dao-tao/' + kdt_id;
        return axios.get(url)
            .then(response => response.data);
    },
    getDanhSachHocKy: function (kdt_id) {
        const url = 'http://localhost/cea-2.1/public/api/khoa-dao-tao/' + kdt_id + '/hoc-ky';
        return axios.get(url)
            .then(response => response.data)
            .then(data => {
                //     // Phân môn học ra từng học kỳ
                //     var semesters = [];
                //     data.forEach(function (item) {
                //         let semesterIndex = item.pivot.kdt_mh_hocky - 1;
                //         if (semesters[semesterIndex] == undefined) {
                //             semesters[semesterIndex] = {
                //                 allow_delete: true,
                //                 monHoc: []
                //             };
                //         }
                //         semesters[semesterIndex].monHoc.push(item);
                //     });
                //     for (let i = 0; i < semesters.length; i++) {
                //         if (semesters[i] == undefined) {
                //             semesters[i] = {
                //                 allow_delete: true,
                //                 monHoc: [{}]
                //             };
                //         } else {
                //             semesters[i].allow_delete = !semesters[i].monHoc.some(mh => mh.bang_diem_exists);
                //         }
                //     }
                //     return semesters;
                // })
                // .then(data => {
                //     // Sắp xếp lại các môn học
                //     data.forEach(semester => {
                //         semester.monHoc.sort((a, b) => a.pivot.kdt_mh_index - b.pivot.kdt_mh_index);
                //     });

                return data;
            });
    },
    getDanhSachMonHoc: function (hdt_id, nn_id) {
        const url = 'http://localhost/cea-2.1/public/api/mon-hoc/all?hdt_id=' + hdt_id + '&nn_id=' + nn_id;
        return axios.get(url)
            .then(response => response.data);
    },
    getListHeDaoTao: function () {
        const url = 'http://localhost/cea-2.1/public/api/he-dao-tao/all';
        return axios.get(url).then(response => response.data);
    },
    getMonHoc: function (mh_id) {
        const url = 'http://localhost/cea-2.1/public/api/mon-hoc/' + mh_id;
        return axios.get(url)
            .then(response => response.data);
    },
    saveOrUpdateMonHoc: function (formData) {
        if (formData.mh_id == null) {
            return axios.post('http://localhost/cea-2.1/public/api/mon-hoc', formData);
        } else {
            return axios.put('http://localhost/cea-2.1/public/api/mon-hoc/' + formData.mh_id, formData);
        }
    },
    saveOrUpdateHocKy: function (kdt_id, danhSachHocKy) {
        return axios.post(`/api/khoa-dao-tao/${kdt_id}/hoc-ky`, { semesters: danhSachHocKy });
    },
    getSyncMonHocTheoNganhNghe: function (data) {
        return axios.post(`/api/khoa-dao-tao/getSyncMonHocTheoNN`, data);
    },
    updateSyncMonHocTheoNN: function (data, kdt_id) {
        return axios.put(`/api/khoa-dao-tao/${kdt_id}/updateSyncMonHocTheoNN`, data);
    }
}

export default {
    props: ['kdt_id', 'parent_url'],
    mounted() {
        this.init();
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
                errors: Object,
            },
            listMonHoc: {
                original: [],
                select2: [],
            },
            listHeDaoTao: {
                select2: [],
            },
            listMonHocSync: [],
        }
    },
    methods: {
        loadDanhSachMonHoc: function (hdt_id, nn_id) {
            consumer.getDanhSachMonHoc(hdt_id, nn_id).then(data => {
                this.listMonHoc.original = data;
                this.listMonHoc.select2 = data.map(item => {
                    return {
                        id: item.mh_id,
                        text: item.mh_ma + ' - ' + item.mh_ten
                    };
                })
            })
        },
        loadKhoaDaoTao: function () {
            var vm = this;
            consumer.getKhoaDaotao(this.kdt_id).then(data => {
                this.editForm.model = data;
                this.loadDanhSachMonHoc(data.hdt_id, data.nn_id);
            });

            if (this.editForm.listSemester) {
                consumer.getDanhSachHocKy(vm.kdt_id)
                    .then(semesters => {
                        if (semesters == 0) {
                            let newNemester = vm.createNewSemesterObject();
                            vm.editForm.listSemester.push(newNemester);
                        } else {
                            vm.editForm.listSemester = semesters;
                        }
                    });
            }
        },
        init: function () {
            this.loadKhoaDaoTao();
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
        actionRemoveMonHoc: function (list, index) {
            if (list.length > 1) {
                list.splice(index, 1);
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
        actionSyncMonHocTheoNganhNghe: function () {
            let requestData = {
                'kdt_id': this.kdt_id,
                'DsMh_kdt_current': this.editForm.listSemester,
                'nn_id': this.editForm.model.nn_id,
            };

            consumer.getSyncMonHocTheoNganhNghe(requestData)
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
        actionUpdateSyncMonHocTheoNganhNghe: function (listMonHocSync) {
            const khoaDaoTao_id = this.kdt_id;

            // Lọc các phần tử có thuộc tính mh_selected là true và thuộc tính tồn tại
            listMonHocSync = listMonHocSync.filter(function (item) {
                return item.mh_selected === true && item.hasOwnProperty('mh_selected');
            });

            const listMonHocSyncReq = {
                listMonHocSync: listMonHocSync
            };

            consumer.updateSyncMonHocTheoNN(listMonHocSyncReq, khoaDaoTao_id)
                .then(response => {
                    if (response.status == 200) {
                        this.loadKhoaDaoTao();
                        AlertBox.Notify.Success('Cập nhật thêm môn học thành công');
                        ayncMonHocModal.hide();
                    }
                })
                .catch(error => {
                    AlertBox.Notify.Error('Cập nhật thêm môn học thất bại!');
                });
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
        actionLuuHocKy: function () {
            var vm = this;


            let listMonHocId = this.editForm.listSemester
                .filter(function (monHoc) {
                    return monHoc.mh_id != undefined;
                })
                .map(function (monHoc) {
                    return monHoc.mh_id;
                });

            let listKdt_mh_apdung = this.editForm.listSemester
                .filter(function (monHoc) {
                    return monHoc.pivot["kdt_mh_apdung"] != undefined;
                })
                .map(function (monHoc) {
                    if (monHoc.pivot["kdt_mh_apdung"] == false)
                        return 0;
                    else
                        return 1;
                });

            let semesters = {
                'mh_ids': listMonHocId,
                'kdt_mh_apdung': listKdt_mh_apdung
            }

            // console.log("log: ", semesters);

            consumer.saveOrUpdateHocKy(this.editForm.model.kdt_id, semesters)
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
    }
}
</script>
