<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                {{ editForm.reference == Object ? 'Thêm người dùng' : editForm.reference.name }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Người dùng</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 no-padding">
                    <div class="col-lg-12 lg-no-padding-left md-no-padding-left">
                        <div class="nav-tabs-custom" v-if="editForm.model != Object">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_1" data-toggle="tab">Thông tin chi tiết</a></li>
                                <li><a href="#tab_2" data-toggle="tab">Quyền hạn</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <form-group :errors="editForm.errors" :field="'cb_ho'">
                                        <label>Họ</label>
                                        <input type="text" class="form-control" v-model="editForm.model.cb_ho" />
                                    </form-group>
                                    <form-group :errors="editForm.errors" :field="'cb_ten'">
                                            <label>Tên</label>
                                            <input type="text" class="form-control" v-model="editForm.model.cb_ten" />
                                        </form-group>
                                    <form-group :errors="editForm.errors" :field="'email'">
                                        <label>Email</label>
                                        <input type="email" class="form-control" v-model="editForm.model.email" />
                                    </form-group>
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
                                    <form-group :errors="editForm.errors" :field="'status'">
                                        <label>Trạng thái</label>
                                        <select class="form-control" v-model="editForm.model.status">
                                            <option value="1">Hoạt động</option>
                                            <option value="0">Ngưng hoạt động</option>
                                        </select>
                                    </form-group>
                                    <form-group :errors="editForm.errors" :field="'cb_chucvu'">
                                        <label>Chức vụ</label>
                                        <input type="text" class="form-control" v-model="editForm.model.cb_chucvu" />
                                    </form-group>
                                    <form-group :errors="editForm.errors" :field="'username'">
                                        <label>Tài khoản</label>
                                        <input type="text" class="form-control" v-model="editForm.model.username" />
                                    </form-group>
                                    <form-group :errors="editForm.errors" :field="'password'">
                                        <label>Mật khẩu</label>
                                        <input type="password" class="form-control" v-model="editForm.model.password" />
                                    </form-group>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_2">
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
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary bg-purple" @click="save"><i class="fa fa-save"></i> Lưu</button>
                                <a href="/user" class="btn btn-default"><i class="fa fa-share"></i> Trở về danh sách</a>
                            </div>
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
        getUser: function (user_id) {
            const url = 'http://localhost/cea-2.0/public/apiuser/' + user_id;
            return axios.get(url)
                    .then(response => response.data);
        },
        saveOrUpdate: function (formData) {
            if (formData.user_id == null) {
                return axios.post('http://localhost/cea-2.0/public/apiuser', formData);
            } else {
                return axios.put('http://localhost/cea-2.0/public/apiuser/' + formData.user_id, formData);
            }
        },
        getListPermission: function () {
            return [
                { permission_id: 'admin.users', name: 'Quản lý người dùng' },
                { permission_id: 'admin.index', name: 'Quản lý danh mục' },
                { permission_id: 'admin.score', name: 'Quản lý điểm sinh viên' },
                { permission_id: 'admin.thitotnghiep', name: 'Quản lý đợt thi tốt nghiệp' },
                { permission_id: 'admin.xettotnghiep', name: 'Quản lý đợt xét tốt nghiệp' },

                { permission_id: 'trungcap', name: 'Trung cấp' },
                { permission_id: 'caodang', name: 'Cao đẳng' },
                { permission_id: 'xemdiem', name: 'Xem điểm' },
                { permission_id: 'nhaprenluyen', name: 'Nhập điểm rèn luyện' },
            ]
        }
    }

    export default {
        props: ['userid'],
        mounted() {
            if (this.userid) {
                this.edit(this.userid);
            } else {
                this.resetEditForm();
            }
            this.permissions = consumer.getListPermission();
        },
        data() {
            return {
                editForm: {
                    reference: Object,
                    model: Object,
                    errors: Object,
                },
                permissions: [],
                toggle: {
                    changePassword: false
                }
            }
        },
        methods: {
            resetEditForm: function () {
                this.editForm = {
                    reference: Object,
                    model: {
                        root: false,
                        status: 1,
                        cb_gioitinh: 1,
                        permissions: [],
                    }
                };
                Vue.set(this.editForm, 'errors', {});
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
                            AlertBox.Notify.Success('Thêm thành công');
                            Vue.set(this.editForm, 'errors', {});
                            if (window.location.href != response.data.payload.url) {
                                window.history.pushState({}, '', response.data.payload.url);
                            }
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        }
                    });
            },
            update: function () {
                var vm = this;
                consumer.saveOrUpdate(this.editForm.model)
                    .then(response => {
                        if (response.status == 200) {
                            AlertBox.Notify.Success('Cập nhật thành công');
                            Vue.set(this.editForm, 'errors', {});
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            Vue.set(vm.editForm, 'errors', error.response.data.errors);
                        }
                    });
            },
            edit: function (user_id) {
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
                })
            }
        }
    }
</script>
