<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Nhật ký / Chi tiết / Sinh viên: {{ sinhVien.sv_ho  +" "+ sinhVien.sv_ten }}
                <!-- {{ sinh_vien }} -->
            </h1>
            <ol class="breadcrumb">
                <li>
                    <a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a>
                </li>
                <li><a href="/nhat-ky">Nhật ký</a></li>
                <li class="active"><a :href="parent_url">DS sinh viên</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div style="margin-bottom:6px;">
                <a :href="parent_url" class="btn btn-default" >
                    <i class="fa fa-share"></i> Trở về danh sách
                </a>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box">
                        <div class="box-body">
                            <form method="get">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Lọc theo</label>
                                            <select name="f_type" class="form-control" v-model="filter.f_type">
                                                <option value="1">Tất cả</option>
                                                <option value="2">Theo thời gian</option>
                                            </select>
                                        </div>
                                    </div>



                                    <div class="col-md-2" v-if="filter.f_type == 2">
                                        <div class="form-group">
                                            <label>Tháng</label>
                                            <select name="tunam" class="form-control" v-model="filter.dt_tunam">
                                                <option value="1">Tháng 1</option>
                                                <option value="2">Tháng 2</option>
                                                <option value="3">Tháng 3</option>
                                                <option value="4">Tháng 4</option>
                                                <option value="5">Tháng 5</option>
                                                <option value="6">Tháng 6</option>
                                                <option value="7">Tháng 7</option>
                                                <option value="8">Tháng 8</option>
                                                <option value="9">Tháng 9</option>
                                                <option value="10">Tháng 10</option>
                                                <option value="11">Tháng 11</option>
                                                <option value="12">Tháng 12</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2" v-if="filter.f_type == 2">
                                        <div class="form-group">
                                            <label>Năm</label>
                                            <select name="dennam" class="form-control" v-model="filter.dt_dennam">
                                                <option value="2023">Năm 2023</option>
                                                <option value="2022">Năm 2022</option>
                                                <option value="2021">Năm 2021</option>
                                                <option value="2020">Năm 2020</option>
                                                <option value="2019">Năm 2019</option>
                                                <option value="2018">Năm 2018</option>
                                                <option value="2017">Năm 2017</option>
                                                <option value="2016">Năm 2016</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Hệ đào tạo</label>
                                            <select name="chuongtrinh" class="form-control" v-model="filter.chuongtrinh">
                                                <option v-for="nk in listHeDaoTao.select2" :key="nk.id"
                                                    v-bind:value="nk.id">
                                                    {{ nk.text }}
                                                </option>
                                            </select>
                                        </div>
                                    </div> -->

                                    <div class="col-md-2" align="center" style="top: 25px;">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fa fa-search"></i> Tìm kiếm
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-body">
                            <table class="table table-striped table-bordered no-margin">
                                <thead>
                                    <tr>
                                        <th class="w-3 text-center">#</th>
                                        <th>Tên sinh viên</th>
                                        <th class="w-14">Mã lớp</th>
                                        <th class="w-14">Tên lớp</th>
                                        <th class="w-14">Hành động</th>
                                        <th class="w-12 text-center">Thời gian truy cập</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(sv, index) in table.list.data" :key="sv.dt_id">
                                        <td class="text-center">
                                            {{
                                                index + 1 + (table.list.current_page - 1) * table.list.per_page
                                            }}
                                        </td>
                                        <td>{{ sv.sinh_vien.sv_ho + " " + sv.sinh_vien.sv_ten }}</td>
                                        <td>{{ sv.sinh_vien.lop_hoc[0].lh_ma }}</td>
                                        <td>{{ sv.sinh_vien.lop_hoc[0].lh_ten }}</td>
                                        <td><i class="fa fa-check" aria-hidden="true" style="color: green;"></i> {{ sv.type
                                             == 1 ? "Đăng nhập" : "Đã xem điểm" }}</td>
                                        <td class="text-center">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i> {{ formatDate(sv.created_at) }}
                                        </td>
                                    </tr>
                                    <tr v-if="table.list.data == null || table.list.data.length == 0">
                                        <td colspan="100" class="text-center">
                                            {{
                                                table.list.data != null ? "Không tìm thấy dữ liệu" : "Đang tải"
                                            }}
                                        </td>
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
    </div>
</template>

<script>
import moment from 'moment';


const consumer = {
    getListnhatKy: function (sv_id) {
        const url = `http://localhost/cea_3.0/public/api/nhat-ky/${sv_id}/paginateNhatKy` + window.location.search;
        return axios.get(url).then((response) => response.data);
    },
    getLisLopHoc: function () {
        const url = "http://localhost/cea_3.0/public/api/lop-hoc/all";
        return axios.get(url).then((response) => response.data);
    },
    getListHeDaoTao: function () {
        const url = "http://localhost/cea_3.0/public/api/he-dao-tao/all";
        return axios.get(url).then((response) => response.data);
    },
};

const store = {
    table: {
        set list(data) {
            window.localStorage.setItem("dt.table.list", JSON.stringify(data));
        },
        get list() {
            return window.localStorage.getItem("dt.table.list")
                ? JSON.parse(window.localStorage.getItem("dt.table.list"))
                : {};
        },
    },
};

export default {
    props: ["permissions", "sv_id", "sinh_vien", "parent_url"],
    mounted() {
        this.quyenTrungCap = this.permissions.includes("trungcap");
        this.quyenCaoDang = this.permissions.includes("caodang");
        if (new URLSearchParams(window.location.search).get("chuongtrinh") != null) {
            this.filter.chuongtrinh = new URLSearchParams(window.location.search).get(
                "chuongtrinh"
            );
        } else if (this.quyenCaoDang) {
            this.filter.chuongtrinh = 4;
        } else if (this.quyenTrungCap) {
            this.filter.chuongtrinh = 5;
        }
        this.reloadList();

        this.sinhVien = JSON.parse(this.sinh_vien);
   },
    data() {
        return {
            loai: Object, //1 là tốt nghiệp 2 là xóa //  3 du thi tn
            dt_id: Object,
            sinhVien: {},
            dotThi: {},
            filter: {
                search: new URLSearchParams(window.location.search).get("search"),
                f_type:
                    new URLSearchParams(window.location.search).get("f_type") == null
                        ? 2
                        : new URLSearchParams(window.location.search).get("f_type"),
                dt_tunam:
                    new URLSearchParams(window.location.search).get("tunam") == null
                        ? new Date().getMonth() + 1
                        : new URLSearchParams(window.location.search).get("tunam"),
                dt_dennam:
                    new URLSearchParams(window.location.search).get("dennam") == null
                        ? 2023
                        : new URLSearchParams(window.location.search).get("dennam")

            },
            editForm: {
                reference: Object,
                model: Object,
                errors: Object,
            },
            table: {
                list: store.table.list,
            },
            listDotXet: {
                select2: [],
            },
            months: Array.from({ length: 13 }, (v, i) => i).slice(1),
            days: Array.from({ length: 31 }, (v, i) => i).slice(1),
            years: Array.from({ length: new Date().getFullYear() - 2019 }, (v, i) => 2020 + i),
            listHeDaoTao: {
                select1: [],
                select2: [],
            },
            editQuyetDinhForm: {
                model: Object,
                errors: Object,
            },

        };
    },
    methods: {
        formatDate(date) {
            return moment(date).format('HH:mm:ss DD/MM/YYYY');
        },
        reloadList: function () {
            var vm = this;

            consumer.getListnhatKy(this.sv_id).then((data) => {
                Vue.set(vm.table, "list", data);
                store.table.list = data;
            });
        }
    },
};
</script>
