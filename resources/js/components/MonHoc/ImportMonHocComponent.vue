<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Thêm môn học bằng file Excel
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Thêm môn học bằng file Excel</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget" id="panel-monhoc">
                        <div class="box-header">
                            <h3 class="box-title">
                                <button class="btn btn-default" onclick="window.history.back()">
                                    <i class="fa fa-share"></i> Trở về danh sách
                                </button>
                                <input type="file" class="hidden" id="import-file"
                                    accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    v-on:change="actionSelectFile" />
                                <button type="button" class="btn btn-default" v-on:click="actionShowSelectFile">
                                    <i class="fa fa-file-excel-o"></i> Chọn tập tin excel
                                </button>
                                <a href="/sample/import-mon-hoc-sample.xls" class="btn btn-default" download>
                                    <i class="fa fa-download"></i> Tải mẫu
                                </a>
                            </h3>
                            <div class="box-tool pull-right form-horizontal">
                                <div v-if="table.list.length > 0" style="width:500px;display:inline-block;">
                                    <label class="col-sm-5 control-label">Ngành, Nghề</label>
                                    <div class="col-sm-7">
                                        <select2 v-model="table.nn_id" :options="listNganhNghe.select2"></select2>
                                    </div>
                                    <errors-validate :errors="table.errors" :field="'nn_id'" />
                                </div>
                                <div v-if="table.list.length > 0" style="max-width:350px;display:inline-block;">
                                    <label class="col-sm-4 control-label">Hệ đào tạo</label>
                                    <div class="input-group col-sm-8">
                                        <select2 v-model="table.hdt_id" :options="listHeDaoTao.select2"></select2>
                                        <span class="input-group-btn">
                                            <button class="btn btn-danger btn-flat" v-on:click="save();">Lưu</button>
                                        </span>
                                    </div>
                                    <errors-validate :errors="table.errors" :field="'password'" />
                                </div>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Mã môn học</th>
                                        <th class="text-center">Tên môn học</th>
                                        <th class="text-center">Số tín chỉ</th>
                                        <th class="text-center">Số tiết/giờ</th>
                                        <th class="text-center">Tích lũy</th>
                                        <th class="text-left">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(mh, index) in table.list" :key="mh.mh_id">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td>
                                            {{ mh.mh_ma }}
                                            <errors-validate :errors="table.errors" :field="'data.' + index + '.mh_ma'" />
                                        </td>
                                        <td>
                                            {{ mh.mh_ten }}
                                            <errors-validate :errors="table.errors" :field="'data.' + index + '.mh_ten'" />
                                        </td>
                                        <td>
                                            {{ mh.mh_sodonvihoctrinh }}
                                            <errors-validate :errors="table.errors"
                                                :field="'data.' + index + '.mh_sodonvihoctrinh'" />
                                        </td>
                                        <td>
                                            {{ mh.mh_sotiet }}
                                            <errors-validate :errors="table.errors"
                                                :field="'data.' + index + '.mh_sotiet'" />
                                        </td>
                                        <td>{{ mh.mh_tichluy ? 'Không' : 'Có' }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-flat btn-sm" v-on:click="preUpdate(index)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-flat btn-sm" v-on:click="deleteItem(index)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="table.list.length == 0">
                                        <td colspan="99" class="text-center">Không tìm thấy dữ liệu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer">
                            <button class="btn btn-default" onclick="window.history.back()">
                                <i class="fa fa-share"></i> Trở về danh sách
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->

        <div class="modal fade" id="mon-hoc-edit-modal">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{{ editForm.reference == Object ? 'Thêm Sinh viên' :
                            editForm.reference.sv_ho + ' ' + editForm.reference.sv_ten }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form-group :errors="editForm.errors" :field="'mh_ma'">
                                    <label>Môn học mã</label>
                                    <input type="text" class="form-control" v-model="editForm.model.mh_ma" />
                                </form-group>
                                <form-group :errors="editForm.errors" :field="'mh_ten'">
                                    <label>Tên môn học</label>
                                    <input type="text" class="form-control" v-model="editForm.model.mh_ten" />
                                </form-group>
                                <form-group :errors="editForm.errors" :field="'mh_sodonvihoctrinh'">
                                    <label>Số tính chỉ</label>
                                    <input type="text" class="form-control" v-model="editForm.model.mh_sodonvihoctrinh" />
                                </form-group>
                                <form-group :errors="editForm.errors" :field="'mh_sotiet'">
                                    <label>Số tiết/giờ</label>
                                    <input type="text" class="form-control" v-model="editForm.model.mh_sotiet" />
                                </form-group>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fa fa-times-circle-o"></i> Hủy bỏ
                        </button>
                        <button type="button" class="btn bg-purple" v-on:click="update()">
                            <i class="fa fa-save"></i> Lưu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="chon-sheet-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Tập tin của bạn có nhiều sheet, vui lòng chọn sheet muốn nhập</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table mb-0" style="margin-bottom:0px">
                            <tr v-for="(sheet, index) in listSheet.list" :key="sheet.id">
                                <td>
                                    <button class="btn btn-primary text-left btn-block" v-on:click="selectSheet(index)"
                                        style="text-align: left;margin-bottom:6px;">
                                        {{ index + 1 }}. {{ sheet.title }}
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const chonSheetModal = {
    modal: function () {
        return $('#chon-sheet-modal');
    },
    show: function () {
        this.modal().modal({ keyboard: false, backdrop: 'static' });
    },
    hide: function () {
        this.modal().modal('hide');
    }
}

const editModal = {
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

const consumer = {
    save: function (formData) {
        return axios.post('http://localhost/tthl11/public/apimon-hoc/them-excel', formData);
    },
    uploadFile: function (file) {
        var formData = new FormData();
        var headers = {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }
        formData.append('excel_file', file);
        return axios.post('http://localhost/tthl11/public/apiexcel/import-mon-hoc', formData, headers);
    },
    getListHeDaoTao: function () {
        const url = 'http://localhost/tthl11/public/apihe-dao-tao/all';
        return axios.get(url).then(response => response.data);
    },
    getListNganhNghe: function () {
        const url = 'http://localhost/tthl11/public/apinganh-nghe/all';
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

const panelMonHoc = {
    selector: function () {
        return '#panel-monhoc';
    },
    block: function () {
        AlertBox.Loading.Block(this.selector(), 'Đang tải');
    },
    unblock: function () {
        AlertBox.Loading.Unblock(this.selector());
    }
}

export default {
    props: ['router', 'permissions'],
    mounted() {
        this.quyenTrungCap = this.permissions.includes('trungcap');
        this.quyenCaoDang = this.permissions.includes('caodang');
        this.loadDanhSachHeDaoTao();
        this.loadDanhSachNganhNghe();
    },
    data() {
        return {
            table: {
                list: [],
                errors: Object,
                hdt_id: 0,
                nn_id: null,
            },
            listSheet: {
                list: [],
            },
            editForm: {
                reference: Object,
                model: {},
                errors: Object,
            },
            listHeDaoTao: {
                select2: [],
            },
            listNganhNghe: {
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
        loadDanhSachHeDaoTao: function () {
            consumer.getListHeDaoTao().then(data => {
                this.listHeDaoTao.select2 = data.filter(item => {
                    return (item.hdt_ten.toLowerCase() == 'cao đẳng' && this.quyenCaoDang) || (item.hdt_ten.toLowerCase() == 'trung cấp' && this.quyenTrungCap);
                }).map(item => {
                    return {
                        id: item.hdt_id,
                        text: item.hdt_ten
                    };
                });
                this.listHeDaoTao.select2 = [{ id: 0, text: 'Dùng chung' }, ...this.listHeDaoTao.select2];
            });
        },
        loadDanhSachNganhNghe: function () {
            consumer.getListNganhNghe().then(data => {
                this.listNganhNghe.select2 = data.filter(item => {
                    return (item.he_dao_tao.hdt_ten.toLowerCase() == 'cao đẳng' && this.quyenCaoDang) || (item.he_dao_tao.hdt_ten.toLowerCase() == 'trung cấp' && this.quyenTrungCap);
                }).map(item => {
                    return {
                        id: item.nn_id,
                        text: item.nn_ten + " - " + item.he_dao_tao.hdt_ten
                    };
                });
            });
        },
        selectSheet: function (number) {
            this.table.list = this.listSheet.list[number].rows;
            chonSheetModal.hide();
        },
        resetEditForm: function () {
            this.editForm = {
                reference: Object,
                model: {},
            };
            Vue.set(this.editForm, 'errors', {});
        },
        preUpdate: function (index) {
            this.resetEditForm();
            this.editForm.model = { ...this.table.list[index] };
            this.editForm.model.index = index;
            this.editForm.reference = { ...this.editForm.model };
            editModal.show();
        },
        update: function () {
            this.table.list[this.editForm.model.index] = this.editForm.model;
            this.resetEditForm();
            editModal.hide();
        },
        save: function () {
            var vm = this;
            panelMonHoc.block();
            vm.table.list.map((value) => {
                value.mh_tichluy = value.mh_tichluy == false ? 1 : 0;
                return value;
            })
            // console.log("mh: ", { data: vm.table.list, hdt_id: this.table.hdt_id, nn_id: this.table.nn_id })
            consumer.save({ data: vm.table.list, hdt_id:this.table.hdt_id, nn_id:this.table.nn_id })
            .then(response => {
                if (response.status == 200) {
                    alert('Thêm thành công');
                    window.location = this.router;
                }
            })
            .catch(error => {
                if (error.response.status == 422) {
                    Vue.set(this.table, 'errors', error.response.data.errors);
                }
                panelMonHoc.unblock();
            });
        },
        actionShowSelectFile: function () {
            document.getElementById('import-file').click();
        },
        actionSelectFile: function (event) {
            if (event.target.files.length) {
                consumer.uploadFile(event.target.files[0])
                    .then((response) => {
                        this.listSheet.list = response.data;
                        if (this.listSheet.list.length > 1) {
                            chonSheetModal.show();
                        }
                        if (this.listSheet.list.length == 1) {
                            this.selectSheet(0)
                        }
                    })
                    .catch(error => {
                        AlertBox.Notify.Failure('Nhập dữ liệu thất bại');
                        console.log(error);
                    });
            }
        },
        deleteItem: function (index) {
            this.table.list.splice(index, 1);
            AlertBox.Notify.Success('Xóa thành công');
        },
    }
}
</script>
