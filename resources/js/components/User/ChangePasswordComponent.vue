<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                {{ editForm.reference == Object ? 'Đang tải' : editForm.reference.name }}
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
                                <li class="active"><a href="#tab_1" data-toggle="tab">Đổi mật khẩu</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <form-group :errors="editForm.errors" :field="'username'">
                                        <label>Tài khoản</label>
                                        <div>
                                            {{ editForm.model.username }}
                                        </div>
                                    </form-group>
                                    <form-group :errors="editForm.errors" :field="'email'">
                                        <label>Email</label>
                                        <div>
                                            {{ editForm.model.email }}
                                        </div>
                                    </form-group>
                                    <form-group :errors="editForm.errors" :field="'password'">
                                        <label>Mật khẩu</label>
                                        <input type="password" class="form-control" v-model="editForm.model.password" />
                                    </form-group>
                                    <form-group :errors="editForm.errors" :field="'password_confirmation'">
                                        <label>Nhập lại mật khẩu</label>
                                        <input type="password" class="form-control" v-model="editForm.model.password_confirmation" />
                                    </form-group>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary bg-purple" @click="save"><i class="fa fa-save"></i> Lưu</button>
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
            const url = 'http://localhost/tthl/public/apiuser/' + user_id;
            return axios.get(url)
                    .then(response => response.data);
        },
        updatePassword: function (formData) {
            return axios.put('http://localhost/tthl/public/apiuser/current/password', formData);
        }
    }

    export default {
        props: ['userid'],
        mounted() {
            this.edit(this.userid);
        },
        data() {
            return {
                editForm: {
                    reference: Object,
                    model: Object,
                    errors: Object,
                }
            }
        },
        methods: {
            resetEditForm: function () {
                this.editForm = {
                    reference: Object,
                    model: {
                        root: false,
                        password: null,
                        password_confirmation: null
                    }
                };
                Vue.set(this.editForm, 'errors', {});
            },
            save: function () {
                var vm = this;
                consumer.updatePassword(this.editForm.model)
                    .then(response => {
                        if (response.status == 200) {
                            AlertBox.Notify.Success('Mật khẩu đã được cập nhật');
                            Vue.set(this.editForm, 'errors', {});
                            this.editForm.model.password = null;
                            this.editForm.model.password_confirmation = null;
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
                        name: data.user.name,
                        permissions: data.permissions,
                        root: data.root
                    };

                    // Object reference => object with old model props
                    this.editForm.reference = { ...this.editForm.model };
                })
            }
        }
    }
</script>
