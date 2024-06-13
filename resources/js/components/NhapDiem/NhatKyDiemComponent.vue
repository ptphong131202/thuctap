<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Nhật ký sửa điểm</h1>
            <ol class="breadcrumb">
                <li>
                    <a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a>
                </li>
                <li class="active">Nhật ký sửa điểm</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-body table-responsive">
                            <table
                                class="table table-striped table-bordered no-margin"
                            >
                                <thead>
                                    <tr>
                                        <th class="w-3 text-center">#</th>
                                        <th class="w-10">Ngày</th>
                                        <th>Cán bộ</th>
                                        <th>Môn học</th>
                                        <th class="w-4">Mã lớp</th>
                                        <th class="w-3 text-center">
                                            Hành động
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(nk, index) in table.list.data"
                                        :key="nk.bd_id"
                                    >
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ nk.thoigian | moment }}</td>
                                        <td>{{ nk.user_info.name }}</td>
                                        <td>{{ nk.mon_hoc.mh_ten }}</td>
                                        <td>{{ nk.lop_hoc.lh_ma }}</td>
                                        <td>
                                            <a
                                                :href="nhapDiemUrl(nk)"
                                                class="btn bg-purple btn-sm"
                                                title="Chi tiết cập nhật"
                                            >
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer">
                            <paginate
                                :paginate="table.list"
                                v-if="table.list != Object"
                            ></paginate>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
</template>

<script>
import moment from "moment";

const consumer = {
    getNhatKy: function () {
        const url =
            "http://localhost/cea-3.0/public/nhap-diem/nhat-ky-diem" +
            window.location.search;
        return axios.get(url).then((response) => response.data);
    },
};

const store = {
    table: {
        set list(data) {
            window.localStorage.setItem("nkd.table.list", JSON.stringify(data));
        },
        get list() {
            return window.localStorage.getItem("nkd.table.list")
                ? JSON.parse(window.localStorage.getItem("nkd.table.list"))
                : {};
        },
    },
};

export default {
    mounted() {
        this.reloadList();
    },
    data() {
        return {
            table: {
                list: store.table.list,
            },
            filter: {
                semester: 123456,
            },
        };
    },
    filters: {
        moment: function (date) { 
            if (date) {
                return moment(date).format("HH:mm DD/MM/yyyy");
            }
            return null;
        },
    },

    methods: {
        reloadList: function () {
            var vm = this;
            consumer.getNhatKy().then((data) => {
                Vue.set(vm.table, "list", data);
                store.table.list = data;
                console.log(data);
            });
        },

        nhapDiemUrl: function (nk) {
            let baseSixFour = btoa(JSON.stringify({  bd_id: nk.bang_diem.bd_id ,
                thoigian: nk.thoigian,
                 mh_id: nk.mon_hoc.mh_id,
                  lh_id: nk.lop_hoc.lh_id}));
            /* return 'http://localhost/cea-3.0/public/nhap-diem/xem-nhat-ky-diem?bd_id=' + nk.bang_diem.bd_id + '&thoigian="' + nk.thoigian + '"&mh_id=' +nk.mon_hoc.mh_id + '&lh_id=' + nk.lop_hoc.lh_id; */
            return 'http://localhost/cea-3.0/public/nhap-diem/xem-nhat-ky-diem/' + encodeURIComponent(baseSixFour);

        }
    }
};
</script>
