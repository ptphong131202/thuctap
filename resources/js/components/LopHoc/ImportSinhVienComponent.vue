<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Thêm sinh viên bằng file Excel
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Thêm sinh viên bằng file Excel</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget" id="panel-sinhvien">
                        <div class="box-header">
                            <h3 class="box-title">
                                <button class="btn btn-default" onclick="window.history.back()"> 
                                    <i class="fa fa-share"></i> Trở về danh sách
                                </button>
                                <input type="file"
                                        class="hidden"
                                        id="import-file"
                                        accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                        v-on:change="actionSelectFile"/>
                                <button type="button" class="btn btn-default" v-on:click="actionShowSelectFile">
                                    <i class="fa fa-file-excel-o"></i> Chọn tập tin excel
                                </button>
                                <a href="/sample/import-sinh-vien-sample.xls" class="btn btn-default" download>
                                    <i class="fa fa-download"></i> Tải mẫu
                                </a>
                            </h3>
                            <div class="box-tool pull-right">
                                <div v-if="table.list.length > 0" style="max-width:300px;display:inline-block;">
                                    <div class="input-group ">
                                        <input type="text" class="form-control" v-model="table.password" placeholder="Mật khẩu mặc định của Sinh viên" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-danger btn-flat" v-on:click="save();">Lưu</button>
                                        </span>
                                    </div>
                                    <errors-validate :errors="table.errors" :field="'password'"/>
                                </div>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-striped table-bordered no-margin">
                                <thead>
                                    <tr >
                                        <th class="text-center">#</th>
                                        <th class="text-center">Mã sinh viên</th>
                                        <th class="text-center">Thông tin sinh viên</th>
                                        <th class="text-center">Dân tộc</th>
                                        <th class="text-center">Trình độ</th>
                                        <th class="text-center" style="width:30%">Liên hệ</th>
                                        <th class="text-left">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(sv, index) in table.list" :key="sv.sv_id">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td >
                                            {{sv.sv_ma}}
                                            <errors-validate :errors="table.errors" :field="'data.' + index + '.sv_ma'"/>
                                        </td>
                                        <td >
                                            <b>{{sv.sv_ho}} {{sv.sv_ten}}</b><br/>
                                            <b>Giới tính: </b> {{ sv.sv_gioitinh == 1 ? 'Nam':'Nữ' }}<br/>
                                            <b>Ngày sinh: </b> {{sv.sv_ngaysinh}} 
                                            <errors-validate :errors="table.errors" :field="'data.' + index + '.sv_ho'"/>
                                            <errors-validate :errors="table.errors" :field="'data.' + index + '.sv_ten'"/>
                                            <errors-validate :errors="table.errors" :field="'data.' + index + '.sv_ngaysinh'"/>
                                        </td>
                                        <td >
                                            {{sv.sv_dantoc}}
                                        </td>
                                        <td >
                                            {{sv.sv_trinhdo}}
                                        </td>
                                        <td >
                                            <span v-if=" sv.sv_diachi != null && sv.sv_diachi != '' "><strong><i class="fa fa-home"/></strong> {{ sv.sv_diachi }}<br/></span>
                                            <span v-if=" sv.sv_sdt != null && sv.sv_sdt != '' "><strong><i class="fa fa-phone"/></strong> {{ sv.sv_sdt }}<br/></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-flat btn-sm"
                                                v-on:click="preUpdate(index)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-flat btn-sm"
                                                v-on:click="deleteItem(index)">
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

    <div class="modal fade" id="sinh-vien-edit-modal">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{{ editForm.reference == Object ? 'Thêm Sinh viên' : editForm.reference.sv_ho +' '+ editForm.reference.sv_ten }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form-group :errors="editForm.errors" :field="'sv_ma'">
                                            <label>MSHS</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_ma" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_ho'">
                                            <label>Họ</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_ho" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_ten'">
                                            <label>Tên</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_ten" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_gioitinh'">
                                            <label>Giới tính</label>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="sv_gioitinh" value="1" v-model="editForm.model.sv_gioitinh" />
                                                    Nam
                                                </label>
                                                <label style="margin-left: 15px">
                                                    <input type="radio" name="sv_gioitinh" value="0" v-model="editForm.model.sv_gioitinh">
                                                    Nữ
                                                </label>
                                            </div>
                                        </form-group>
                                        <form-group :errors="editForm.errors" :field="'sv_dantoc'">
                                            <label>Dân tộc</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_dantoc" />
                                        </form-group>
                                    </div>
                                    <div class="col-md-6">
                                        <form-group :errors="editForm.errors" :field="'sv_ngaysinh'">
                                            <label>Ngày sinh</label>
                                            <input type="text" class="form-control" v-model="editForm.model.sv_ngaysinh" />
                                        </form-group>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <form-group :errors="editForm.errors" :field="'sv_trinhdo'">
                                                    <label>Trình độ</label>
                                                    <input type="text" class="form-control" v-model="editForm.model.sv_trinhdo" />
                                                </form-group>
                                            </div>
                                            <div class="col-md-8">
                                                <form-group :errors="editForm.errors" :field="'sv_sdt'">
                                                    <label>SĐT</label>
                                                    <input type="number" class="form-control" v-model="editForm.model.sv_sdt" />
                                                </form-group>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form-group :errors="editForm.errors" :field="'sv_diachi'">
                                    <label>Địa chỉ</label>
                                    <input type="text" class="form-control" v-model="editForm.model.sv_diachi" />
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Tập tin của bạn có nhiều sheet, vui lòng chọn sheet muốn nhập</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table mb-0" style="margin-bottom:0px">
                            <tr v-for="(sheet, index) in listSheet.list" :key="sheet.id">
                                <td>
                                    <button class="btn btn-primary text-left btn-block" v-on:click="selectSheet(index)" style="text-align: left;margin-bottom:6px;">
                                        {{index + 1}}. {{sheet.title}}
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
            return $('#sinh-vien-edit-modal');
        },
        show: function () {
            this.modal().modal({ keyboard: false, backdrop: 'static' });
        },
        hide: function () {
            this.modal().modal('hide');
        }
    }

    const consumer = {
        save: function (formData, lh_id) {
            return axios.post('http://localhost/cea-3.0/public/api/lop-hoc/'+lh_id+'/them-sinh-vien-excel', formData);
        },
        uploadFile: function (file) {
            var formData = new FormData();
            var headers = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
            formData.append('excel_file', file);
            return axios.post('http://localhost/cea-3.0/public/api/excel/import-user', formData, headers);
        }
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

    const panelSinhVien = {
        selector: function () {
            return '#panel-sinhvien';
        },
        block: function () {
            AlertBox.Loading.Block(this.selector(), 'Đang tải');
        },
        unblock: function () {
            AlertBox.Loading.Unblock(this.selector());
        }
    }

    export default {
        props: [ 'lh_id', 'router' ],
        mounted() {
        },
        data() {
            return {
                table: {
                    list: [],
                    errors: Object,
                    password: '',
                },
                listSheet: {
                    list: [],
                },
                editForm: {
                    reference: Object,
                    model: {},
                    errors: Object,
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
            selectSheet: function (number) {
                this.table.list = this.listSheet.list[number].rows;
                chonSheetModal.hide();
            },
            resetEditForm: function () {
                this.editForm = {
                    reference: Object,
                    model: {
                        sv_gioitinh: 1,
                        qd_id: -1,
                        lh_id: this.lh_id,
                    },
                };
                Vue.set(this.editForm, 'errors', {});
            },
            preUpdate: function(index){
                this.resetEditForm();
                this.editForm.model = { ...this.table.list[index] };
                this.editForm.model.index = index;
                this.editForm.reference = { ...this.editForm.model };
                editModal.show();
            },
            update: function(){
                this.table.list[this.editForm.model.index] = this.editForm.model;
                this.resetEditForm();
                editModal.hide();
            },
            save: function(){
                var vm = this;
                if(this.lh_id != null){
                    panelSinhVien.block();
                    consumer.save({ data: vm.table.list, password:this.table.password }, this.lh_id)
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
                        panelSinhVien.unblock();
                    });
                }
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
            deleteItem: function(index) {
                this.table.list.splice(index, 1);
                AlertBox.Notify.Success('Xóa thành công');
            },
        }
    }
</script>
