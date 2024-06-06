<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> 
                Người dùng
                <small> 
                    <a href="/user/create" class="btn btn-danger btn-flat btn-xs"
                            title="Thêm mới">
                        <i class="fa fa-plus"></i> Thêm mới
                    </a>
                </small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Người dùng</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-body">
                            <div class="row" style="margin-bottom: 10px;">
                                <form method="get">
                                    <div class="col-lg-4 pull-right">
                                        <div class="input-group">
                                            <input type="text" name="search" v-model="filter.search" class="form-control" placeholder="Tìm kiếm..">
                                            <span class="input-group-btn">
                                                <button type="submit" class="btn btn-default">Tìm</button>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <table class="table table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="w-3 text-center">#</th>
                                        <th class="w-10">Mã số</th>
                                        <th class="w-15">Email</th>
                                        <th>Tên người dùng</th>
                                        <th class="w-15">Chức vụ</th>
                                        <th class="w-10">Trạng thái</th>
                                        <th class="w-100-p text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, index) in table.list.data" :key="user.user_id">
                                        <td class="text-center">{{ index + 1 + ((table.list.current_page - 1) * table.list.per_page) }}</td>
                                        <td>{{ user.cb_ma }}</td>
                                        <td>{{ user.email }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.cb_chucvu }}</td>
                                        <td>{{ user.status == 1 ? 'Hoạt động' : 'Ngưng hoạt động' }}</td>
                                        <td class="text-center">
                                            <!-- <button type="button" class="btn bg-orange btn-sm"
                                                    title="Thay đổi"
                                                    v-on:click="edit(user.user_id)"><i class="fa fa-edit"></i>
                                            </button> -->
                                            <a :href="user.edit_url" class="btn bg-orange btn-sm"
                                                    title="Thay đổi">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    title="Xóa"
                                                    v-on:click="destroy(user.user_id)"><i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="table.list.data == null || table.list.data.length == 0">
                                        <td colspan="100" class="text-center">{{ table.list.data != null ? 'Không tìm thấy dữ liệu' : 'Đang tải' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer">
                            <paginate :paginate="table.list" v-if="table.list != Object"></paginate>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->

        <div class="modal fade" id="user-edit-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{{ editForm.reference == Object ? 'Thêm người dùng' : editForm.reference.name }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <form-group :errors="editForm.errors" :field="'username'">
                                    <label>Tài khoản</label>
                                    <input type="text" class="form-control" v-model="editForm.model.username" />
                                </form-group>
                                <form-group :errors="editForm.errors" :field="'password'">
                                    <label>Mật khẩu</label>
                                    <input type="password" class="form-control" v-model="editForm.model.password" />
                                </form-group>
                                <form-group :errors="editForm.errors" :field="'permission'">
                                    <label>Quyền hạn</label>
                                    <div class="checkbox" v-for="permission in permissions" :key="permission.permission_id">
                                        <label>  
                                            <input type="checkbox" :disabled="editForm.model.root" :value="permission.permission_id" v-model="editForm.model.permissions">
                                            {{ permission.name }}
                                        </label>
                                    </div>
                                </form-group>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <form-group :errors="editForm.errors" :field="'cb_ho'">
                                            <label>Họ</label>
                                            <input type="text" class="form-control" v-model="editForm.model.cb_ho" />
                                        </form-group>
                                    </div>
                                    <div class="col-lg-6">
                                        <form-group :errors="editForm.errors" :field="'cb_ten'">
                                            <label>Tên</label>
                                            <input type="text" class="form-control" v-model="editForm.model.cb_ten" />
                                        </form-group>
                                    </div>
                                </div>
                                <form-group :errors="editForm.errors" :field="'email'">
                                    <label>Email</label>
                                    <input type="email" class="form-control" v-model="editForm.model.email" />
                                </form-group>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <form-group :errors="editForm.errors" :field="'cb_gioitinh'">
                                            <label>Giới tính</label>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="cb_gioitinh" value="1" v-model="editForm.model.cb_gioitinh" />
                                                    Nam
                                                </label>
                                                <label style="margin-left: 15px">
                                                    <input type="radio" name="cb_gioitinh" value="0" v-model="editForm.model.cb_gioitinh">
                                                    Nữ
                                                </label>
                                            </div>
                                        </form-group>
                                    </div>
                                    <div class="col-lg-6">
                                        <form-group :errors="editForm.errors" :field="'status'">
                                            <label>Trạng thái</label>
                                            <select class="form-control" v-model="editForm.model.status">
                                                <option value="1">Hoạt động</option>
                                                <option value="0">Ngưng hoạt động</option>
                                            </select>
                                        </form-group>
                                    </div>
                                </div>
                                <form-group :errors="editForm.errors" :field="'cb_chucvu'">
                                    <label>Chức vụ</label>
                                    <input type="text" class="form-control" v-model="editForm.model.cb_chucvu" />
                                </form-group>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> Hủy bỏ</button>
                        <button type="button" class="btn bg-purple" v-on:click="save"><i class="fa fa-save"></i> Lưu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const editModal = {
        modal: function () {
            return $('#user-edit-modal');
        },
        show: function () {
            this.modal().modal({ keyboard: false, backdrop: 'static' });
        },
        hide: function () {
            this.modal().modal('hide');
        }
    }

    const consumer = {
        getListUser: function () {
            const url = 'http://localhost/cea-2.1/public/api/user/paginate' + window.location.search; /** Phong API */
            return axios.get(url)
                    .then(response => response.data);
        },
        getUser: function (hdt_id) {
            const url = 'http://localhost/cea-2.1/public/api/user/' + hdt_id;/** Phong API */
            return axios.get(url)
                    .then(response => response.data);
        },
        saveOrUpdate: function (formData) {
            if (formData.user_id == null) {
                return axios.post('http://localhost/cea-2.1/public/api/user', formData);/** Phong API */
            } else {
                return axios.put('http://localhost/cea-2.1/public/api/user/' + formData.user_id, formData);/** Phong API */
            }
        },
        destroy: function (user_id) {
            return axios.delete('http://localhost/cea-2.1/public/api/user/' + user_id);/** Phong API */
        },
        getListPermission: function () {
            return [
                { permission_id: 'administrators', name: 'Quản trị' },
                { permission_id: 'admin.users', name: 'Quản lý người dùng' },
                { permission_id: 'admin.index', name: 'Quản lý danh mục' },
                { permission_id: 'admin.score', name: 'Quản lý điểm sinh viên' },
                { permission_id: 'trungcap', name: 'Trung cấp' },
                { permission_id: 'caodang', name: 'Cao đẳng' },
                { permission_id: 'xemdiem', name: 'Xem điểm' },
                { permission_id: 'nhaprenluyen', name: 'Nhập điểm rèn luyện' },
            ]
        }
    }

    const store = {
        table: {
            set list(data) {
                window.localStorage.setItem('user.table.list', JSON.stringify(data));
            },
            get list() {
                return window.localStorage.getItem('user.table.list') ? JSON.parse(window.localStorage.getItem('user.table.list')) : {};
            }
        }
    }

    export default {
        mounted() {
            this.reloadList();
        },
        data() {
            return {
                filter: {
                    search: new URLSearchParams(window.location.search).get('search')
                },
                editForm: {
                    reference: Object,
                    model: Object,
                    errors: Object,
                },
                table: {
                    list: store.table.list,
                },
                permissions: []
            }
        },
        methods: {
            reloadList: function () {
                var vm = this;
                consumer.getListUser().then(data => {
                    Vue.set(vm.table, 'list', data);
                    store.table.list = data;    
                });
            },
            resetEditForm: function () {
                this.editForm = {
                    reference: Object,
                    model: {
                        root: false,
                        status: 1,
                        permissions: [],
                        cb_gioitinh: 1,
                    }
                };
                this.permissions = consumer.getListPermission();
                Vue.set(this.editForm, 'errors', {});
            },
            create: function () {
                this.resetEditForm();
                editModal.show();
            },
            save: function () {
                if (this.editForm.model.user_id == null) {
                    this.store();
                } else {
                    this.update();
                }
            },
            store: function () {
                var vm = this;
                consumer.saveOrUpdate(this.editForm.model)
                    .then(response => {
                        if (response.status == 200) {
                            vm.resetEditForm();
                            vm.reloadList();
                            editModal.hide();
                            AlertBox.Notify.Success('Thêm thành công');
                            console.log(response);
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        }
                    });
            },
            edit: function (user_id) {
                this.resetEditForm();
                consumer.getUser(user_id).then(data => {
                    // Object edit model
                    this.editForm.model = {
                        user_id: data.user.user_id,
                        username: data.user.username,
                        email: data.user.email,
                        status: data.user.status,
                        permissions: data.permissions,
                        root: data.root,
                        cb_ho: data.user.can_bo.cb_ho,
                        cb_ten: data.user.can_bo.cb_ten,
                        cb_gioitinh: data.user.can_bo.cb_gioitinh,
                        cb_chucvu: data.user.can_bo.cb_chucvu,
                    };

                    // Object reference => object with old model props
                    this.editForm.reference = { ...this.editForm.model };
                    if (this.editForm.reference.cb_ho) {
                        this.editForm.reference.name = this.editForm.reference.cb_ho + ' ' + this.editForm.reference.cb_ten;
                    } else {
                        this.editForm.reference.name = this.editForm.reference.cb_ten;
                    }

                    editModal.show();
                })
            },
            update: function () {
                var vm = this;
                consumer.saveOrUpdate(this.editForm.model)
                    .then(response => {
                        if (response.status == 200) {
                            vm.resetEditForm();
                            vm.reloadList();
                            editModal.hide();
                            AlertBox.Notify.Success('Cập nhật thành công');
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        }
                    });
            },
            destroy: function (user_id) {
                var vm = this;
                AlertBox.Comfirm('Xác nhận',
                    'Bạn có đồng ý xóa',
                    function () {
                        // Ok
                        consumer.destroy(user_id).then(response => {
                            vm.reloadList();
                            AlertBox.Notify.Success('Xóa thành công');
                        })
                        .catch(error => {
                            AlertBox.Notify.Failure('Không thể xóa');
                        })
                    },
                    function () {
                        // No
                    })
            }
        }
    }
</script>
