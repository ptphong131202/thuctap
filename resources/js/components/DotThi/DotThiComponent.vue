<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Đợt thi
                <small>
                    <a href="javascript:void(0);" class="btn btn-danger btn-flat btn-xs" title="Thêm mới"
                        v-on:click="create()">
                        <i class="fa fa-plus"></i> Thêm mới
                    </a>
                </small>
            </h1>
            <ol class="breadcrumb">
                <li>
                    <a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a>
                </li>
                <li class="active">Đợt thi</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box">
                        <div class="box-body">
                            <form method="get">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tên đợt thi</label>
                                            <input type="text" name="search" v-model="filter.search" class="form-control" />
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Trạng thái</label>
                                            <select name="trangthai" class="form-control" v-model="filter.dt_ketthuc">
                                                <option value="-1">Tất cả</option>
                                                <option value="0">Đang hoạt động</option>
                                                <option value="1">Đã kết thúc</option>
                                            </select>
                                        </div>
                                    </div> -->


                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Từ năm</label>
                                            <select name="tunam" class="form-control" v-model="filter.dt_tunam">
                                                <option v-for="year in getYears()" :value="year">{{ year }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Đến năm</label>
                                            <select name="dennam" class="form-control" v-model="filter.dt_dennam">
                                                <option v-for="year in getYears()" :value="year">{{ year }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Hệ đào tạo</label>
                                            <select name="chuongtrinh" class="form-control" v-model="filter.chuongtrinh">
                                                <option v-for="nk in listHeDaoTao.select2" :key="nk.id"
                                                    v-bind:value="nk.id">
                                                    {{ nk.text }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
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
                                        <th>Đợt thi</th>
                                        <!-- <th>Trạng thái</th> -->
                                        <th class="w-14">Số kế hoạch</th>
                                        <th class="w-14">Quyết định</th>
                                        <!-- <th class="w-15">Đợt xét tốt nghiệp</th> -->
                                        <th class="w-12 text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(dt, index) in table.list.data" :key="dt.dt_id">
                                        <td class="text-center">
                                            {{
                                                index + 1 + (table.list.current_page - 1) * table.list.per_page
                                            }}
                                        </td>
                                        <td>{{ dt.dt_ten }}</td>
                                        <!-- <td>
                                            <span class="text-danger" v-if="dt.dt_ketthuc == 1"><i
                                                    class="fa fa-check-square-o" aria-hidden="true"></i> Đã kết
                                                thúc</span>
                                            <span class="text-success" v-if="dt.dt_ketthuc == 0"><i class="fa fa-circle"
                                                    aria-hidden="true"></i> Đang hoạt
                                                động</span>
                                        </td> -->
                                        <td class="text-left">
                                            <p>{{ dt.dt_so_ke_hoach }}</p>
                                        </td>
                                        <td class="text-left">
                                            <p v-if="dt.qd_id != -1">{{ dt.qd_ma + ', ' + formatDate(dt.qd_ngay) }}</p>
                                            <p v-if="dt.qd_id == -1">Không chọn</p>
                                        </td>
                                        <!-- <td class="text-left">
                                            <div v-for="dxtn in dt.dot_xet_tot_nghiep" :key="dxtn.dtxt_id">
                                                {{ dxtn.dxtn_ten }}
                                            </div>
                                            <button v-on:click="preThemDotXet(dt.dt_id)" title="Thêm đợt xét" v-if="dt.dot_xet_tot_nghiep.length == 0 &&
                                                dt.qd_ten &&
                                                dt.dt_qd_trangthai == 1
                                                " class="btn btn-success btn-sm">
                                                <i class="fa fa-plus"></i> Thêm đợt xét
                                            </button>
                                            <button title="Thêm đợt xét"
                                                v-if="dt.dot_xet_tot_nghiep.length == 0 && !dt.qd_ten"
                                                class="btn btn-success btn-sm" disabled>
                                                <i class="fa fa-plus"></i> Thêm đợt xét
                                            </button>
                                        </td> -->
                                        <td class="text-center">
                                            <button type="button" class="btn bg-red btn-sm" title="Mở khóa"
                                                v-if="dt.dt_qd_trangthai == 1"
                                                v-on:click="updateQd_trang_thai(dt.dt_id, 0)">
                                                <i class="fa fa-lock" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn bg-green btn-sm" title="Khóa lại"
                                                v-if="dt.dt_qd_trangthai == 0"
                                                v-on:click="updateQd_trang_thai(dt.dt_id, 1)">
                                                <i class="fa fa-unlock" aria-hidden="true"></i>
                                            </button>

                                            <a :href="'dot-thi/' + dt.dt_id" class="btn bg-purple btn-sm"
                                                title="Chi tiết đợt thi" v-if="dt.dot_thi_bang_diem_exists">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn bg-orange btn-sm" title="Thay đổi"
                                                v-on:click="edit(dt.dt_id)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" title="Xóa"
                                                v-if="!dt.dot_thi_bang_diem_exists" v-on:click="destroy(dt.dt_id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
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

        <div class="modal fade" id="dot-thi-edit-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">
                            {{
                                editForm.reference == Object
                                ? "Thêm kế hoạch thi"
                                : "Kế hoạch thi" + editForm.reference.dt_ten
                            }}
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form-group :errors="editForm.errors" :field="'dt_ten'">
                            <label>Đợt thi</label>
                            <input type="text" class="form-control" v-model="editForm.model.dt_ten" />
                        </form-group>
                        <div class="row">
                            <div class="col-md-6">
                                <form-group :errors="editForm.errors" :field="'dt_tungay'">
                                    <label>Từ ngày</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select name="dt_tungay" class="form-control"
                                                v-model="editForm.model.dt_tungay">
                                                <option v-for="day in days" :key="day" :value="day">
                                                    {{ day }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="dt_tuthang" class="form-control"
                                                v-model="editForm.model.dt_tuthang">
                                                <option v-for="month in months" :key="month" :value="month">
                                                    {{ month }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="dt_tunam" class="form-control" v-model="editForm.model.dt_tunam">
                                                <option v-for="year in years" :key="year" :value="year">
                                                    {{ year }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </form-group>
                            </div>
                            <div class="col-md-6">
                                <form-group :errors="editForm.errors" :field="'dt_denngay'">
                                    <label>Đến ngày</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select name="dt_denngay" class="form-control"
                                                v-model="editForm.model.dt_denngay">
                                                <option v-for="day in days" :key="day" :value="day">
                                                    {{ day }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="dt_denthang" class="form-control"
                                                v-model="editForm.model.dt_tuthang">
                                                <option v-for="month in months" :key="month" :value="month">
                                                    {{ month }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="dt_dennam" class="form-control"
                                                v-model="editForm.model.dt_dennam">
                                                <option v-for="year in years" :key="year" :value="year">
                                                    {{ year }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </form-group>
                            </div>
                        </div>
                        <form-group :errors="editForm.errors" :field="'dt_ghichu'">
                            <label>Ghi chú</label>
                            <textarea v-model="editForm.model.dt_ghichu" class="form-control" />
                        </form-group>
                        <div class="row">
                            <!-- <div class="col-md-6">
                                <form-group :errors="editForm.errors" :field="'dt_ghichu'">
                                    <label>Kết thúc</label>
                                    <select name="dt_ketthuc" class="form-control" v-model="editForm.model.dt_ketthuc">
                                        <option value="0">Đang sử dụng</option>
                                        <option value="1">Đã kết thúc</option>
                                    </select>
                                </form-group>
                            </div> -->
                            <div class="col-md-6" v-if="editForm.reference == Object">
                                <div class="form-group">
                                    <label>Hệ đào tạo</label>
                                    <input type="text" class="form-control"
                                        :value="filter.chuongtrinh == 5 ? 'Trung cấp' : 'Cao đẳng'" readonly />

                                    <!-- <select name="chuongtrinh" class="form-control" v-model="editForm.model.chuongtrinh">
                                            <option v-for="nk in listHeDaoTao.select2" :key="nk.id"
                                                    v-bind:value="nk.id">
                                                    {{ nk.text }}
                                            </option>
                                        </select> -->
                                </div>
                            </div>

                            <div class="col-md-6">
                                <form-group :errors="editForm.errors" :field="'dt_so_ke_hoach'">
                                    <label>Số kế hoạch</label>
                                    <input type="text" class="form-control" v-model="editForm.model.dt_so_ke_hoach" />
                                </form-group>
                            </div>
                        </div>



                        <form-group v-show="loai == 1" :errors="editQuyetDinhForm.errors" :field="'qd_id'">
                            <label>Quyết định</label>
                            <select2 v-model="editQuyetDinhForm.model.qd_id" :options="listQuyetDinhDuxetThiTN.select2"
                                id="qd_options">
                            </select2>
                        </form-group>

                        <div class="row" v-if="(editQuyetDinhForm.model.qd_id == 0 && loai == 1)">
                            <div class="col-md-6">
                                <form-group :errors="editQuyetDinhForm.errors" :field="'qd_ma'">
                                    <label>Số Quyết định</label>
                                    <input type="text" class="form-control" v-model="editQuyetDinhForm.model.qd_ma" />
                                </form-group>
                            </div>
                            <div class="col-md-6">
                                <form-group :errors="editQuyetDinhForm.errors" :field="'qd_ngay'">
                                    <label>Ngày Quyết định</label>
                                    <input type="date" class="form-control" v-model="editQuyetDinhForm.model.qd_ngay" />
                                </form-group>
                            </div>
                            <div class="col-md-12">
                                <form-group :errors="editQuyetDinhForm.errors" :field="'qd_ten'">
                                    <label>Nội dung quyết định</label>
                                    <textarea class="form-control" v-model="editQuyetDinhForm.model.qd_ten" />
                                </form-group>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fa fa-times-circle-o"></i> Hủy bỏ
                        </button>
                        <button type="button" class="btn bg-purple" v-on:click="save">
                            <i class="fa fa-save"></i> Lưu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Them dot xet -->
        <div class="modal fade" id="modal-chondotxet">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Thêm đợt xét tốt nghiệp</h4>
                    </div>
                    <div class="modal-body">
                        <select2 v-model="dotThi.dxtn_id" :options="listDotXet.select2"></select2>
                    </div>

                    <div class="modal-footer">
                        <button type="button" v-on:click="themDotXet()" class="btn btn-danger">
                            Lưu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="quyet-dinh-edit-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Quyết định dự thi tốt nghiệp</h4>
                    </div>
                    <div class="modal-body">
                        <form-group v-show="loai == 1" :errors="editQuyetDinhForm.errors" :field="'qd_id'">
                            <label>Quyết định</label>
                            <select2 v-model="editQuyetDinhForm.model.qd_id" :options="listQuyetDinhDuxetThiTN.select2">
                            </select2>
                        </form-group>

                        <div class="row" v-if="(editQuyetDinhForm.model.qd_id == 0 && loai == 1)
                            ">
                            <div class="col-md-6">
                                <form-group :errors="editQuyetDinhForm.errors" :field="'qd_ma'">
                                    <label>Số Quyết định</label>
                                    <input type="text" class="form-control" v-model="editQuyetDinhForm.model.qd_ma" />
                                </form-group>
                            </div>
                            <div class="col-md-6">
                                <form-group :errors="editQuyetDinhForm.errors" :field="'qd_ngay'">
                                    <label>Ngày Quyết định</label>
                                    <input type="date" class="form-control" v-model="editQuyetDinhForm.model.qd_ngay" />
                                </form-group>
                            </div>
                            <div class="col-md-12">
                                <form-group :errors="editQuyetDinhForm.errors" :field="'qd_ten'">
                                    <label>Nội dung quyết định</label>
                                    <textarea class="form-control" v-model="editQuyetDinhForm.model.qd_ten" />
                                </form-group>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fa fa-times-circle-o"></i> Hủy bỏ
                        </button>
                        <button type="button" class="btn bg-purple" v-on:click="saveQuyetDinh(dt_id)">
                            <i class="fa fa-save"></i> Lưu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';

const editModal = {
    modal: function () {
        return $("#dot-thi-edit-modal");
    },
    show: function () {
        this.modal().modal({ keyboard: false, backdrop: "static" });
    },
    hide: function () {
        this.modal().modal("hide");
    },
};

const quyetQinhModal = {
    modal: function () {
        return $("#quyet-dinh-edit-modal");
    },
    show: function () {
        this.modal().modal({ keyboard: false, backdrop: "static" });
    },
    hide: function () {
        this.modal().modal("hide");
    },
};

const consumer = {
    getListDotThi: function () {
        const url = "http://localhost/tthl11/public/api/dot-thi" + window.location.search;
        return axios.get(url).then((response) => response.data);
    },
    getListDotXet: function (chuongtrinh) {
        const url = "http://localhost/tthl11/public/api/dot-xet-tot-nghiep/all?chuongtrinh=" + chuongtrinh;
        return axios.get(url).then((response) => response.data);
    },
    getDotThi: function (dt_id) {
        const url = "http://localhost/tthl11/public/api/dot-thi/" + dt_id;
        return axios.get(url).then((response) => response.data);
    },
    saveOrUpdate: function (formData) {
        if (formData.dt_id == null) {
            return axios.post("http://localhost/tthl11/public/api/dot-thi", formData);
        } else {
            return axios.put("http://localhost/tthl11/public/api/dot-thi/" + formData.dt_id, formData);
        }
    },
    updateQdTrangThai: function (formData) {
        return axios.put("http://localhost/tthl11/public/api/dot-thi/updateQd-trang-thai/" + formData.dt_id, formData);
    },
    destroy: function (dt_id) {
        return axios.delete("http://localhost/tthl11/public/api/dot-thi/" + dt_id);
    },
    capNhatDotXet: function (formData) {
        return axios.post("http://localhost/tthl11/public/api/dot-xet-tot-nghiep/cap-nhat-dot-xet-cho-dot-thi", formData);
    },
    getListQuyetDinh: function (loai) {
        const url = "http://localhost/tthl11/public/api/quyet-dinh/all/" + 3;
        return axios.get(url).then((response) => response.data);
    },
    saveOrAddQuyetDinh: function (formData) {
        return axios.post("http://localhost/tthl11/public/api/sinh-vien/them-quyet-dinh-thi-tot-nghiep", formData);
    },
    getListHeDaoTao: function () {
        const url = "http://localhost/tthl11/public/api/he-dao-tao/all";
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
    props: ["permissions"],
    mounted() {
        /* this.quyenTrungCap = this.permissions.includes("trungcap");
        this.quyenCaoDang = this.permissions.includes("caodang"); */
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
        this.reloadQuyetDinh();
    },
    data() {
        return {
            quyenTrungCap: true,
            quyenCaoDang: true,
            loai: Object, //1 là tốt nghiệp 2 là xóa //  3 du thi tn
            dt_id: Object,
            dotThi: {},
            filter: {
                search: new URLSearchParams(window.location.search).get("search"),
                dt_loai:
                    new URLSearchParams(window.location.search).get("loai") == null
                        ? -1
                        : new URLSearchParams(window.location.search).get("loai"),
                dt_ketthuc:
                    new URLSearchParams(window.location.search).get("trangthai") == null
                        ? -1
                        : new URLSearchParams(window.location.search).get("trangthai"),
                dt_tunam:
                    new URLSearchParams(window.location.search).get("tunam") == null
                        ? new Date().getFullYear()
                        : new URLSearchParams(window.location.search).get("tunam"),
                dt_dennam:
                    new URLSearchParams(window.location.search).get("dennam") == null
                        ? new Date().getFullYear()
                        : new URLSearchParams(window.location.search).get("dennam"),
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
            listQuyetDinhDuxetThiTN: {
                select2: [],

            },
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
        getYears() {
            const currentYear = new Date().getFullYear();
            const years = [];
            for (let i = currentYear; i >= currentYear - 10; i--) {
                years.push(i.toString()); // Chuyển đổi số năm thành chuỗi và thêm vào mảng
            }
            return years;
        },
        formatDate(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        preThemDotXet: function (dt_id) {
            this.dotThi.dt_id = dt_id;
            $("#modal-chondotxet").modal("show");
        },
        themDotXet: function () {
            consumer.capNhatDotXet(this.dotThi).then((data) => {
                $("#modal-chondotxet").modal("hide");
                this.reloadList();
                AlertBox.Notify.Success("Cập nhật thành công");
            });
        },
        reloadQuyetDinh: function () {
            var vm = this;

            // consumer.getListQuyetDinh().then((data) => {
            let danhSachQd = [
                { id: -1, text: "Không chọn" },
                { id: 0, text: "Quyết định mới" },
            ];
            // danhSachQd.push(
            //     ...data.map((item) => {
            //         return {
            //             id: item.qd_id,
            //             text: item.qd_ten,
            //         };
            //     })
            // );
            vm.listQuyetDinhDuxetThiTN = {
                select2: danhSachQd,
            };
            // });
        },
        reloadList: function () {
            var vm = this;
            if (vm.listDotXet.select2.length == 0) {
                let chuongtrinh = this.filter.chuongtrinh;
                consumer.getListDotXet(chuongtrinh).then((data) => {
                    vm.listDotXet.select2 = data.map((item) => {
                        return {
                            id: item.dxtn_id,
                            text: item.dxtn_ten,
                        };
                    });
                });
            }
            consumer.getListDotThi().then((data) => {
                Vue.set(vm.table, "list", data);
                store.table.list = data;
            });

            if (vm.listQuyetDinhDuxetThiTN.select2.length == 0) {
                // consumer.getListQuyetDinh().then((data) => {
                vm.listQuyetDinhDuxetThiTN.select2.push({ id: -1, text: "Không chọn" });
                vm.listQuyetDinhDuxetThiTN.select2.push({ id: 0, text: "Quyết định mới" });
                //     vm.listQuyetDinhDuxetThiTN.select2.push(
                //         ...data.map((item) => {
                //             return {
                //                 id: item.qd_id,
                //                 text: item.qd_ten,
                //             };
                //         })
                //     );
                // });
            }

            if (vm.listHeDaoTao.select2.length == 0) {
                consumer.getListHeDaoTao().then((data) => {
                    vm.listHeDaoTao.select2 = data
                        .filter((item) => {
                            return (
                                (item.hdt_ten.toLowerCase() == "cao đẳng" && this.quyenCaoDang) ||
                                (item.hdt_ten.toLowerCase() == "trung cấp" && this.quyenTrungCap)
                            );
                        })
                        .map((item) => {
                            return {
                                id: item.hdt_id,
                                text: item.hdt_ten,
                            };
                        });


                });
            }

        },
        reloadFormDotThi: function () {
            var vm = this;

            if (vm.listQuyetDinhDuxetThiTN.select2.length == 0) {
                // consumer.getListQuyetDinh().then((data) => {
                vm.listQuyetDinhDuxetThiTN.select2.push({ id: -1, text: "Không chọn" });
                vm.listQuyetDinhDuxetThiTN.select2.push({ id: 0, text: "Quyết định mới" });
                //     vm.listQuyetDinhDuxetThiTN.select2.push(
                //         ...data.map((item) => {
                //             return {
                //                 id: item.qd_id,
                //                 text: item.qd_ten,
                //             };
                //         })
                //     );
                // });
            }

            if (vm.listHeDaoTao.select2.length == 0) {
                consumer.getListHeDaoTao().then((data) => {
                    vm.listHeDaoTao.select2 = data
                        .filter((item) => {
                            return (
                                (item.hdt_ten.toLowerCase() == "cao đẳng" && this.quyenCaoDang) ||
                                (item.hdt_ten.toLowerCase() == "trung cấp" && this.quyenTrungCap)
                            );
                        })
                        .map((item) => {
                            return {
                                id: item.hdt_id,
                                text: item.hdt_ten,
                            };
                        });
                });
            }
        },
        initialQuyetDinh: function (loai) {
            this.resetQuyetDinhEditForm();
            this.loai = loai;
        },
        preOpenQuyetDinh: function (dt_id) {
            this.resetQuyetDinhEditForm();
            this.loai = 1;
            this.dt_id = dt_id;
            quyetQinhModal.show();
        },
        saveQuyetDinh: function () {
            var vm = this;
            var obj = this.editQuyetDinhForm.model;
            obj.dt_id = this.dt_id;
            obj.qd_id = this.editQuyetDinhForm.model.qd_id;

            consumer
                .saveOrAddQuyetDinh(obj)
                .then((response) => {
                    if (response.status == 200) {
                        vm.reloadList();
                        vm.reloadQuyetDinh();
                        quyetQinhModal.hide();
                        AlertBox.Notify.Success("Thêm thành công");
                    }
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        Vue.set(vm.editQuyetDinhForm, "errors", error.response.data.errors);
                    }
                });
        },
        resetEditForm: function () {
            this.editForm = {
                reference: Object,
                model: {
                    dt_loai: 1,
                    dt_tungay: 1,
                    dt_denngay: 1,
                    dt_tuthang: 1,
                    dt_denthang: 1,
                    dt_tunam: new Date().getFullYear(),
                    dt_dennam: new Date().getFullYear(),
                },
            };

            Vue.set(this.editForm, "errors", {});
        },
        resetQuyetDinhEditForm: function () {
            this.editQuyetDinhForm = {
                model: {
                    qd_id: -1,
                    qd_id: -1,
                },
            };
            Vue.set(this.editQuyetDinhForm, "errors", {});
        },
        create: function () {
            this.initialQuyetDinh(1);
            this.resetEditForm();
            this.reloadFormDotThi();
            editModal.show();
        },
        save: function () {
            if (this.editForm.model.dt_id == null) {
                this.store();
            } else {
                this.update();
            }
        },
        store: function () {
            var vm = this;
            var obj = this.editQuyetDinhForm.model;
            obj.qd_id = this.editQuyetDinhForm.model.qd_id;
            obj.dt_hdt_id = this.filter.chuongtrinh

            var doThidObj = { ...obj, ...this.editForm.model };
            console.log(doThidObj);
            consumer
                .saveOrUpdate(doThidObj)
                .then((response) => {
                    if (response.status == 200) {
                        vm.resetEditForm();
                        vm.reloadList();
                        editModal.hide();
                        AlertBox.Notify.Success("Thêm thành công");
                    }
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        Vue.set(vm.editForm, "errors", error.response.data.errors);
                    }
                });
        },
        edit: function (dt_id) {
            this.initialQuyetDinh(1);
            // this.resetEditForm();

            consumer.getDotThi(dt_id).then((data) => {
                this.editForm.model = data;
                if (data.qd_id == -1) {
                    this.editQuyetDinhForm.model.qd_id = -1;
                    this.editQuyetDinhForm.model.qd_id_root = -1;
                } else {
                    this.editQuyetDinhForm.model = data.quyet_dinh;
                    this.editQuyetDinhForm.model.qd_id_root = data.quyet_dinh.qd_id;
                    this.editQuyetDinhForm.model.qd_id = 0;
                }

                this.editForm.reference = { ...this.editForm.model };
                editModal.show();
            });
        },
        update: function () {
            var vm = this;
            var obj = this.editQuyetDinhForm.model;
            obj.qd_id = this.editQuyetDinhForm.model.qd_id;
            obj.qd_id_root = this.editQuyetDinhForm.model.qd_id_root;
            var editFormObj = { ...this.editForm.model };
            delete editFormObj.qd_id;
            var doThidObj = { ...obj, ...editFormObj };

            // console.log(doThidObj);

            consumer.saveOrUpdate(doThidObj)
                .then((response) => {
                    if (response.status == 200) {
                        vm.resetEditForm();
                        vm.reloadList();

                        vm.reloadQuyetDinh();
                        editModal.hide();
                        AlertBox.Notify.Success("Cập nhật thành công");
                    }
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        Vue.set(vm.editForm, "errors", error.response.data.errors);
                    }
                });
        },
        updateQd_trang_thai: function (dt_id, status) {
            var vm = this;
            var obj = this.editQuyetDinhForm.model;

            var formData = { ...obj, "dt_id": dt_id, "dt_qd_trangthai": status };
            consumer
                .updateQdTrangThai(formData)
                .then((response) => {
                    if (response.status == 200) {
                        vm.resetEditForm();
                        vm.reloadList();
                        AlertBox.Notify.Success("Thay đổi thành công");
                    }
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        Vue.set(vm.editForm, "errors", error.response.data.errors);
                    }
                });
        },
        destroy: function (dt_id) {
            var vm = this;
            AlertBox.Comfirm(
                "Xác nhận",
                "Bạn có đồng ý xóa",
                function () {
                    // Ok
                    consumer
                        .destroy(dt_id)
                        .then((response) => {
                            vm.reloadList();
                            AlertBox.Notify.Success("Xóa thành công");
                        })
                        .catch((error) => {
                            vm.reloadList();
                            AlertBox.Notify.Failure("Không thể xóa");
                        });
                },
                function () { }
            );
        }
    },
};
</script>
