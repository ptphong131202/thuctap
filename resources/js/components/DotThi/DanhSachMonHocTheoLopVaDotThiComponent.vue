<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Danh sách môn học lớp {{ view.lh_ten }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
                <li class="active">Danh sách môn học</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <a :href="'/dot-thi/'+view.dt_id" class="btn btn-default" style="margin-bottom:6px;">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>
            <a :href="view.url_import" title="Nhập điễm từ file excel" class="btn btn-success btn-sm pull-right" v-if="!view.qd">
                <i class="fa fa-file-excel-o"></i> Nhập điểm thi
            </a>
            <a title="Nhập điễm từ file excel" class="btn btn-success btn-sm pull-right" v-if="view.qd" style="cursor: not-allowed">
                <i class="fa fa-file-excel-o"></i> Nhập điểm thi
            </a>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-header with-border">
                            <div class="box-title">Đợt thi {{view.dt_ten}}</div>
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-hover table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="text-center w-3">STT</th>
                                        <th class="w-10">Mã môn học</th>
                                        <th class="w-100">Tên môn học</th>
                                        <th class="w-100">Loại</th>
                                        <th class="w-7 text-center" style="min-width: 150px">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(mh, mIndex) in tableDotThi.list" :key="'mh' +mIndex">
                                        <td class="text-center">{{ mIndex + 1 }}</td>
                                        <td>
                                            <span>{{ mh.mh_ma }}</span>
                                        </td>
                                        <td>
                                            <span>{{ mh.mh_ten }}</span>
                                        </td>
                                        <td>
                                            <span v-if="mh.mh_loai == 1">Bình thường</span>
                                            <span v-if="mh.mh_loai == 2">Thi chính trị</span>
                                            <span v-if="mh.mh_loai == 3">Thi tốt nghiệp thực hành</span>
                                            <span v-if="mh.mh_loai == 4">Thi tốt nghiệp lý thuyết</span>
                                            <span v-if="mh.mh_loai == 5">Thi tốt nghiệp khóa luận</span>
                                        </td>
                                        <td v-if="!view.qd">
                                            <a :href="{ ...mh, dotthi: view.dt_id, lh_id: lh_id } | nhapDiemDotThiUrl"
                                                    class="btn bg-purple btn-sm"
                                                    :title="'Nhập điểm môn ' + mh.mh_ten">
                                                <i class="fa fa-pencil"></i> Nhập điểm
                                            </a>
                                        </td>
                                        <td v-if="view.qd">
                                            <a class="btn bg-purple btn-sm" style="cursor: not-allowed"
                                                    :title="'Nhập điểm môn ' + mh.mh_ten">
                                                <i class="fa fa-pencil"></i> Nhập điểm
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <a :href="'/dot-thi/'+view.dt_id" class="btn btn-default">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>
        </section>
        <!-- /.content -->
    </div>
</template>

<script>
    const consumer = {
        getDanhSachMonHoc: function (lh_id, dt_id) {
            const url = 'http://localhost/tthl11/public/api/dot-thi/'+dt_id+'/dsmon?lh_id=' + lh_id+'&dt_id='+dt_id;
            return axios.get(url).then(response => response.data);
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
        props: ['qd', 'lh_id', 'lh_ten', 'dt_id', 'dt_ten' , 'url_import'],
        mounted() {
            this.init();
        },
        data() {
            return {
                view:{
                    qd: this.qd,
                    lh_id: this.lh_id,
                    lh_ten: this.lh_ten,
                    dt_id: this.dt_id,
                    dt_ten: this.dt_ten,
                    url_import: this.url_import
                },
                model: Object,
                tableDotThi:{
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
            nhapDiemDotThiUrl: function (monHoc) {
                let baseSixFour = btoa(JSON.stringify({ lh_id: monHoc.lh_id, dot_thi: monHoc.dotthi, mh_id: monHoc.mh_id }));
                return 'http://localhost/tthl11/public/api/nhap-diem/dot-thi/' + baseSixFour;
            },
        },
        methods: {
            reloadList: function (lh_id, dt_id) {
                var vm = this;
                consumer.getDanhSachMonHoc(lh_id, dt_id).then(data => {
                    Vue.set(vm.tableDotThi, 'list', data);
                    store.table.list = data;
                });
            },
            init: function () {
                this.reloadList(this.view.lh_id, this.view.dt_id);
            },
            destroyBangDiemDotThi: function (dt_bd_id) {
                var vm = this;
                AlertBox.Comfirm('Xác nhận',
                    'Bạn có đồng ý xóa',
                    function () {
                        consumer.destroyBangDiemDotThi(dt_bd_id).then(response => {
                            vm.reloadList(vm.view.lh_id,vm.view.dt_id);
                            AlertBox.Notify.Success('Xóa thành công');
                        })
                    },
                    function () {

                    })
            }

        }
    }
</script>
