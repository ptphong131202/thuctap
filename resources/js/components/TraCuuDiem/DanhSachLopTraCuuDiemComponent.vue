<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Tra cứu điểm
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
                            <table class="table table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="w-3 text-center">#</th>
                                        <th class="w-10">Mã lớp</th>
                                        <th>Lớp học</th>
                                        <th>Niên khóa</th>
                                        <th>Chương trình đào tạo</th>
                                        <th class="w-5 text-center">Học kỳ</th>
                                        <th class="w-100-p text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(lh, index) in table.list" :key="index">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td>{{ lh.lh_ma }}</td>
                                        <td>{{ lh.lh_ten }}</td>
                                        <td>{{ lh.nk_ten }}</td>
                                        <td>{{ lh.kdt_ten }}</td>
                                        <td class="text-center">{{ lh.kdt_mh_hocky }}</td>
                                        <td class="text-center">
                                            <a :href="lh.detail_url" class="btn bg-purple btn-sm"><i class="fa fa-eye"></i> Xem điểm</a>
                                        </td>
                                    </tr>
                                    <tr v-if="table.list == null || table.list.length == 0">
                                        <td colspan="99" class="text-center">{{ table.list == null ? 'Đang tải' : 'Không tìm thấy dữ liệu' }}</td>
                                    </tr>
                                </tbody>
                            </table>
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
        getListLopHoc: function () {
            const url = 'http://localhost/cea-2.1/public/apitra-cuu-diem/danh-sach-lop';
            return axios.get(url).then(response => response.data);
        }
    }

    const store = {
        table: {
            set list(data) {
                window.localStorage.setItem('tra-cuu-diem.table.list', JSON.stringify(data));
            },
            get list() {
                return window.localStorage.getItem('tra-cuu-diem.table.list') ? JSON.parse(window.localStorage.getItem('tra-cuu-diem.table.list')) : null;
            }
        }
    }

    export default {
        mounted() {
            this.reloadList();
        },
        data() {
            return {
                table: {
                    list: store.table.list, 
                }
            }
        },
        methods: {
            reloadList: function () {
                var vm = this;
                consumer.getListLopHoc().then(data => {
                    Vue.set(vm.table, 'list', data);
                    store.table.list = data;
                });
            }
        }
    }
</script>
