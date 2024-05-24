<template>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header" v-if="editForm.lopHoc != Object">
      <h1>
        Nhập điểm lớp {{ editForm.lopHoc.lh_ma }}
        <small> Học kỳ {{ editForm.model.kdt_hocky }} </small>
      </h1>
      <ol class="breadcrumb">
        <li>
          <a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a>
        </li>
        <li class="active">Lớp học</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div style="margin-bottom: 6px">
        <a :href="'/tthl/public/nhap-diem/' + lh_id" class="btn btn-default">
          <i class="fa fa-share"></i> Trở về danh sách
        </a>
        <button type="button" class="btn bg-purple" v-on:click="actionSave">
          <i class="fa fa-save"></i> Lưu
        </button>
      </div>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="box box-widget">
            <div class="box-header with-border">
              <div class="box-title">
                {{ editForm.monHoc.mh_ma }} - {{ editForm.monHoc.mh_ten }}
              </div>
            </div>
            <div class="box-body table-responsive">
              <div class="row">
                <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                    <label>Giảng viên</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="editForm.model.bd_giangvien"
                    />
                  </div>
                </div>
                <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                    <label>Thời gian thực hiện từ ngày</label>
                    <!-- <input type="text" class="form-control datepicker" v-model="editForm.model.bd_tungay"/> -->
                    <div class="input-group date datetimepicker">
                      <input
                        id="bd_tungay"
                        type="text"
                        class="form-control"
                        v-model="editForm.model.bd_tungay_formated"
                      />
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                    <label>Đến ngày</label>
                    <!-- <input type="text" class="form-control datepicker" v-model="editForm.model.bd_denngay"/> -->
                    <div class="input-group date datetimepicker">
                      <input
                        id="bd_denngay"
                        type="text"
                        class="form-control"
                        v-model="editForm.model.bd_denngay_formated"
                      />
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div style="margin-bottom: 10px">
                <div class="btn-group">
                  <button type="button" class="btn btn-default">
                    <i class="fa fa-file-excel-o"></i> Nhập từ tập tin excel
                  </button>
                  <button
                    type="button"
                    class="btn btn-default dropdown-toggle"
                    data-toggle="dropdown"
                    aria-expanded="false"
                  >
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a
                        href="javascript:void(0)"
                        v-on:click="actionShowSelectFile(true)"
                      >
                        {{ editForm.lopHoc.lh_nienche == 1 ? "QC2022" : "QC2020" }} - Tập
                        tin chứa công thức
                      </a>
                    </li>
                    <li>
                      <a
                        href="javascript:void(0)"
                        v-on:click="actionShowSelectFile(false)"
                      >
                        {{ editForm.lopHoc.lh_nienche == 1 ? "QC2022" : "QC2020" }} - Tập
                        tin không chứa công thức
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn btn-default">
                    <i class="fa fa-download"></i> Tải mẫu
                  </button>
                  <button
                    type="button"
                    class="btn btn-default dropdown-toggle"
                    data-toggle="dropdown"
                    aria-expanded="false"
                  >
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="/sample/import-diem-2022-sample.xls"
                        >QC2022 - Mẫu có công thức</a
                      >
                    </li>
                    <li>
                      <a href="/sample/import-diem-2022-raw-sample.xls"
                        >QC2022 - Mẫu không có công thức</a
                      >
                    </li>
                    <li>
                      <a href="/sample/import-diem-sample.xls"
                        >QC2020 - Mẫu có công thức</a
                      >
                    </li>
                    <li>
                      <a href="/sample/import-diem-raw-sample.xls"
                        >QC2020 - Mẫu không có công thức</a
                      >
                    </li>
                  </ul>
                </div>
              </div>
              <div id="panel-diem">
                <table class="table table-hover table-striped table-bordered no-margin">
                  <thead>
                    <tr>
                      <th rowspan="3" class="w-3 text-center">#</th>
                      <th rowspan="3" class="text-center w-5">
                        MSSV
                        <input
                          v-model="filter.sv_ma"
                          v-on:keyup="changeFilter"
                          type="text"
                          class="form-control input-sm"
                          placeholder="Tìm kiếm MSSV"
                        />
                      </th>
                      <th rowspan="3" colspan="2" class="text-center">
                        Họ và tên
                        <input
                          v-model="filter.sv_hoten"
                          v-on:keyup="changeFilter"
                          type="text"
                          class="form-control input-sm"
                          placeholder="Tìm kiếm theo tên..."
                        />
                      </th>
                      <th rowspan="3" class="w-10 text-center" style="min-width: 85px">
                        Dự lớp (%)
                      </th>
                      <th v-bind:colspan="quyChe2022 ? 8 : 3" class="w-30 text-center">
                        Điểm
                      </th>
                      <th rowspan="3" class="w-15 text-center" style="min-width: 100px">Ghi chú</th>
                      <!-- <th colspan="2" class="w-20 text-center" style="min-width: 170px">Ghi chú</th> -->
                    </tr>
                    <tr>
                      <th
                        v-bind:colspan="quyChe2022 ? 2 : 1"
                        class="text-center"
                        :style="quyChe2022 ? 'width: 70px;' : ''"
                      >
                        Lần 1
                      </th>
                      <th v-bind:colspan="quyChe2022 ? 3 : 2" class="text-center">
                        Lần 2
                      </th>
                      <th v-if="quyChe2022" colspan="3" class="text-center">Lần 3</th>
                      <!-- <th class="text-center">QĐ tốt nghiệp</th>
                                            <th class="text-center">QĐ xóa tên</th> -->
                    </tr>
                    <tr>
                      <!-- Lần 1 -->
                      <th
                        v-if="quyChe2022"
                        class="text-center"
                        :style="quyChe2022 ? 'width: 70px;' : ''"
                      >
                        Điểm thi
                      </th>
                      <th class="text-center" :style="quyChe2022 ? 'width: 70px;' : ''">
                        Điểm TK
                      </th>
                      <!-- Lần 2 -->
                      <th v-if="quyChe2022" class="text-center" style="min-width: 70px">
                        Điểm thi
                      </th>
                      <th class="text-center" style="min-width: 70px">Điểm TK</th>
                      <th class="text-center" style="min-width: 120px">
                        Thời điểm cải thiện
                      </th>
                      <!-- Lần 3 -->
                      <th v-if="quyChe2022" class="text-center" style="min-width: 70px">
                        Điểm thi
                      </th>
                      <th v-if="quyChe2022" class="text-center" style="min-width: 70px">
                        Điểm TK
                      </th>
                      <th v-if="quyChe2022" class="text-center" style="min-width: 120px">
                        Thời điểm cải thiện
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(sv, indexSv) in editForm.model.data"
                      :key="indexSv"
                      v-show="sv.show"
                    >
                      <td class="text-center">{{ indexSv + 1 }}</td>
                      <td class="text-center">{{ sv.sv_ma }}</td>
                      <td class="w-10">{{ sv.sv_ho }}</td>
                      <td class="w-10">{{ sv.sv_ten }}</td>
                      <td>
                        <form-group
                          :errors="editForm.errors"
                          :field="'data.' + indexSv + '.svd_dulop'"
                        >
                          <input
                            type="number"
                            v-model="sv.svd_dulop"
                            class="form-control"
                          />
                        </form-group>
                      </td>
                      <td v-if="quyChe2022">
                        <div style="min-width: 50px">
                          <form-group
                            :errors="editForm.errors"
                            :field="'data.' + indexSv + '.svd_exam_first'"
                          >
                            <input
                              type="number"
                              v-model="sv.svd_exam_first"
                              class="form-control"
                            />
                          </form-group>
                        </div>
                      </td>
                      <td>
                        <div style="min-width: 50px">
                          <form-group
                            :errors="editForm.errors"
                            :field="'data.' + indexSv + '.svd_first'"
                          >
                            <input
                              type="number"
                              v-model="sv.svd_first"
                              class="form-control"
                            />
                          </form-group>
                        </div>
                      </td>
                      <td v-if="quyChe2022">
                        <form-group
                          :errors="editForm.errors"
                          :field="'data.' + indexSv + '.svd_exam_second'"
                        >
                          <input
                            type="number"
                            v-model="sv.svd_exam_second"
                            class="form-control"
                          />
                        </form-group>
                      </td>
                      <td>
                        <form-group
                          :errors="editForm.errors"
                          :field="'data.' + indexSv + '.svd_second'"
                        >
                          <input
                            type="number"
                            v-model="sv.svd_second"
                            class="form-control"
                          />
                        </form-group>
                      </td>
                      <td>
                        <select class="form-control" v-model="sv.svd_second_hocky">
                          <option value="1">Năm I, HK 1</option>
                          <option value="2">Năm I, HK 2</option>
                          <option value="3">Năm II, HK 1</option>
                          <option value="4">Năm II, HK 2</option>
                          <option value="5">Năm III, HK 1</option>
                          <option value="6">Năm III, HK 2</option>
                        </select>
                      </td>
                      <td v-if="quyChe2022">
                        <form-group
                          :errors="editForm.errors"
                          :field="'data.' + indexSv + '.svd_exam_third'"
                        >
                          <input
                            type="number"
                            v-model="sv.svd_exam_third"
                            class="form-control"
                          />
                        </form-group>
                      </td>
                      <td v-if="quyChe2022">
                        <form-group
                          :errors="editForm.errors"
                          :field="'data.' + indexSv + '.svd_third'"
                        >
                          <input
                            type="number"
                            v-model="sv.svd_third"
                            class="form-control"
                          />
                        </form-group>
                      </td>
                      <td v-if="quyChe2022">
                        <select class="form-control" v-model="sv.svd_third_hocky">
                          <option value="1">Năm I, HK 1</option>
                          <option value="2">Năm I, HK 2</option>
                          <option value="3">Năm II, HK 1</option>
                          <option value="4">Năm II, HK 2</option>
                          <option value="5">Năm III, HK 1</option>
                          <option value="6">Năm III, HK 2</option>
                        </select>
                      </td>
                      <td>
                        <input type="text" v-model="sv.svd_ghichu" class="form-control" />
                      </td>
                      <!-- <td>
                                                <span v-if="sv.qdtn_ma" :title="sv.qdtn_ten">{{ sv.qdtn_ma }}, {{ sv.qdtn_ngay | moment }}</span>
                                            </td>
                                            <td>
                                                <span v-if="sv.qdxt_ma" :title="sv.qdxt_ten">{{ sv.qdxt_ma }}, {{ sv.qdxt_ngay | moment }}</span>
                                            </td> -->
                    </tr>
                    <tr v-if="editForm.model.data == null || editForm.model.data == 0">
                      <td colspan="100" class="text-center">
                        {{
                          editForm.model.data != null
                            ? "Không tìm thấy dữ liệu"
                            : "Đang tải"
                        }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="box-footer">
              <a :href="'/tthl/public/nhap-diem/' + lh_id" class="btn btn-default">
                <i class="fa fa-share"></i> Trở về danh sách
              </a>
              <input
                type="file"
                class="hidden"
                id="import-file"
                accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                v-on:change="actionSelectFile"
              />
              <input
                type="file"
                class="hidden"
                id="import-file-raw"
                accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                v-on:change="actionSelectFile"
              />
              <button type="button" class="btn bg-purple" v-on:click="actionSave">
                <i class="fa fa-save"></i> Lưu
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
    <div id="select-sheet" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
              Tập tin của bạn có nhiều sheet, vui lòng chọn sheet muốn nhập
            </h4>
          </div>
          <div class="modal-body">
            <button
              v-for="(sheet, sIndex) in excel.sheets"
              :key="sIndex"
              style="text-align: left"
              class="btn btn-primary btn-block"
              v-on:click="actionSelectSheet(sheet)"
            >
              {{ sheet.number }}. {{ sheet.title }} ({{ sheet.rows.length }})
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const consumer = {
  getLopHoc: function (lh_id) {
    const url = "'http://localhost/tthl/public/api/lop-hoc/" + lh_id;
    return axios.get(url).then((response) => response.data);
  },
  getMonHoc: function (mh_id) {
    const url = "'http://localhost/tthl/public/api/mon-hoc/" + mh_id;
    return axios.get(url).then((response) => response.data);
  },
  getDanhSachHocKy: function (kdt_id) {
    const url = "'http://localhost/tthl/public/api/khoa-dao-tao/" + kdt_id + "/hoc-ky";
    return axios
      .get(url)
      .then((response) => response.data)
      .then((data) => {
        // Phân môn học ra từng học kỳ
        var semesters = [];
        data.forEach(function (item) {
          let semesterIndex = item.pivot.kdt_mh_hocky - 1;
          if (semesters[semesterIndex] == undefined) {
            semesters[semesterIndex] = {
              monHoc: [],
            };
          }
          semesters[semesterIndex].monHoc.push(item);
        });
        for (let i = 0; i < semesters.length; i++) {
          if (semesters[i] == undefined) {
            semesters[i] = {
              monHoc: [],
            };
          }
        }

        return semesters;
      })
      .then((data) => {
        // Sắp xếp lại các môn học
        data.forEach((semester) => {
          semester.monHoc.sort((a, b) => a.pivot.kdt_mh_index - b.pivot.kdt_mh_index);
        });
        return data;
      });
  },
  getBangDiem: function (lh_id, mh_id, hocKy, type) {
    // bd_type = 1 là bảng điểm môn học
    const url = `http://localhost/tthl/public/api/nhap-diem/${lh_id}/bang-diem?mh_id=${mh_id}&hocky=${hocKy}&bd_type=${type}`;
    return axios.get(url).then((response) => response.data);
  },
  saveBangDiem: function (formData) {
    const url = `http://localhost/tthl/public/api/nhap-diem/${formData.lh_id}/bang-diem`;
    return axios.post(url, formData);
  },
  uploadFile: function (file, quyChe2022, hasCal) {
    var formData = new FormData();
    var headers = {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    };
    formData.append("excel_file", file);
    formData.append("quy_che_2022", quyChe2022);
    let url = "http://localhost/tthl/public/api/excel/import-raw-score";
    if (hasCal) {
      url = "http://localhost/tthl/public/api/excel/import-score";
    }
    return axios.post(url, formData, headers);
  },
};

const selectSheetModel = {
  modal: function () {
    return $("#select-sheet");
  },
  show: function () {
    this.modal().modal({ keyboard: false, backdrop: "static" });
  },
  hide: function () {
    this.modal().modal("hide");
  },
};

const panelDiem = {
  selector: function () {
    return "#panel-diem";
  },
  block: function () {
    AlertBox.Loading.Block(this.selector(), "Đang tải");
  },
  unblock: function () {
    AlertBox.Loading.Unblock(this.selector());
  },
};

const BANGDIEMTYPE = {
  MONHOC: 1,
  HOCKY: 2,
};

export default {
  props: ["lh_id", "hoc_ky", "mh_id"],
  mounted() {
    this.loadLopHoc();
  },
  updated: function () {
    this.fixEditDate();
  },
  data() {
    return {
      filter: {
        sv_ma: "",
        sv_hoten: "",
      },
      quyChe2022: false,
      editForm: {
        lopHoc: Object,
        monHoc: Object,
        model: {
          bd_type: 1,
          kdt_hocky: this.hoc_ky,
          mh_id: this.mh_id,
          lh_id: this.lh_id,
          data: [],
        },
        errors: Object,
      },
      excel: {
        sheets: [],
        seledtedSheet: Object,
      },
    };
  },
  filters: {
    moment: function (date) {
      if (date) {
        return moment(date).format("DD/MM/yyyy");
      }
      return null;
    },
  },
  methods: {
    fixEditDate: function () {
      $(".datetimepicker").datetimepicker({
        format: "DD/MM/YYYY",
      });
    },
    loadLopHoc: function () {
      var vm = this;
      // Load thông tin lớp học
      consumer.getLopHoc(this.lh_id).then((lopHoc) => {
        this.editForm.lopHoc = lopHoc;
        this.quyChe2022 = lopHoc.lh_nienche == 1;

        // Load danh sách học kỳ
        this.reloadSinhVien();
      });

      consumer.getMonHoc(this.mh_id).then((monHoc) => {
        this.editForm.monHoc = monHoc;
      });
    },
    reloadSinhVien: function () {
      let lh_id = this.lh_id;
      let mh_id = this.editForm.model.mh_id;
      let hocKy = this.editForm.model.kdt_hocky;
      panelDiem.block();
      consumer.getBangDiem(lh_id, mh_id, hocKy, BANGDIEMTYPE.MONHOC).then((data) => {
        data.data = data.data.filter((item) => {
          let passXoaTen = false;
          let passTotNghiep = false;
          if (
            item.quyetDinhXoaTen == null ||
            item.quyetDinhXoaTen.pivot.qd_hocky >= hocKy
          ) {
            passXoaTen = true;
          }
          if (
            item.quyetDinhTotNghiep == null ||
            item.quyetDinhTotNghiep.pivot.qd_hocky >= hocKy
          ) {
            passTotNghiep = true;
          }
          return passXoaTen && passTotNghiep;
        });
        data.data.forEach((item) => {
          item.show = true;
          item.svd_second_old = item.svd_second;
        });

        let dateOptions = { year: "numeric", month: "2-digit", day: "2-digit" };
        if (data.bd_tungay) {
          data.bd_tungay_formated = moment(data.bd_tungay, "YYYY-MM-DD").format(
            "DD/MM/YYYY"
          );
        }

        if (data.bd_denngay) {
          data.bd_denngay_formated = moment(data.bd_denngay, "YYYY-MM-DD").format(
            "DD/MM/YYYY"
          );
        }

        this.editForm.model = data;
        panelDiem.unblock();
      });
    },
    actionSave: function () {
      var vm = this;

      var data = {
        bd_type: this.editForm.model.bd_type,
        lh_id: this.lh_id,
        mh_id: this.editForm.model.mh_id,
        kdt_hocky: this.editForm.model.kdt_hocky,
        bd_giangvien: this.editForm.model.bd_giangvien,
        bd_tungay: null,
        bd_denngay: null,
        data: this.editForm.model.data,
      };

      let bd_tungay_formated = document.getElementById("bd_tungay").value;
      if (bd_tungay_formated) {
        data.bd_tungay = moment(bd_tungay_formated, "DD/MM/YYYY").format("YYYY-MM-DD");
      }
      this.editForm.model.bd_tungay = data.bd_tungay;

      let bd_denngay_formated = document.getElementById("bd_denngay").value;
      if (bd_denngay_formated) {
        data.bd_denngay = moment(bd_denngay_formated, "DD/MM/YYYY").format("YYYY-MM-DD");
      }
      this.editForm.model.bd_denngay = data.bd_denngay;
      if (data.bd_tungay) {
        this.editForm.model.bd_tungay_formated = moment(
          data.bd_tungay,
          "YYYY-MM-DD"
        ).format("DD/MM/YYYY");
      }

      if (data.bd_denngay) {
        this.editForm.model.bd_denngay_formated = moment(
          data.bd_denngay,
          "YYYY-MM-DD"
        ).format("DD/MM/YYYY");
      }

      consumer
        .saveBangDiem(data)
        .then((response) => {
          vm.editForm.errors = {};
          AlertBox.Notify.Success("Lưu thành công");
        })
        .catch((error) => {
          console.log(error.response);
          if (error.response.status == 422) {
            Vue.set(vm.editForm, "errors", error.response.data.errors);
          } else {
            AlertBox.Notify.Failure("Cập nhật thất bại");
          }
        });
    },
    actionShowSelectFile: function (cal) {
      if (cal) {
        document.getElementById("import-file").click();
      } else {
        document.getElementById("import-file-raw").click();
      }
    },
    actionSelectFile: function (event) {
      // if (event.target.files.length) {
      let hasCal = false;
      if (event.target.id == "import-file") {
        hasCal = true;
      }
      let quyChe2022 = this.editForm.lopHoc.lh_nienche == 1;
      consumer
        .uploadFile(event.target.files[0], quyChe2022, hasCal)
        .then((response) => {
          this.excel.sheets = response.data;
          if (this.excel.sheets.length > 1) {
            selectSheetModel.show();
          }
          if (this.excel.sheets.length == 1) {
            this.actionSelectSheet(this.excel.sheets[0]);
          }
        })
        .catch((error) => {
          AlertBox.Notify.Failure("Nhập dữ liệu thất bại");
          console.log(error);
        });
      // }
    },
    actionSelectSheet: function (sheet) {
      try {
        this.editForm.model.data.forEach((sinhVien) => {
          let found = sheet.rows.find((row) => {
            return row.sv_ma.toUpperCase() === sinhVien.sv_ma.toUpperCase();
          });
          if (found) {
            sinhVien.svd_dulop = found.svd_dulop;

            sinhVien.svd_first = found.svd_first;
            sinhVien.svd_exam_first = found.svd_exam_first;

            sinhVien.svd_second = found.svd_second;
            sinhVien.svd_exam_second = found.svd_exam_second;

            sinhVien.svd_third = found.svd_third;
            sinhVien.svd_exam_third = found.svd_exam_third;
            sinhVien.svd_ghichu = found.svd_ghichu;
          }
        });
        if (this.excel.sheets.length > 1) {
          selectSheetModel.hide();
        }
        document.getElementById("import-file").value = "";
        document.getElementById("import-file-raw").value = "";
        AlertBox.Notify.Success("Nhạp dữ liệu thành công, vui lòng chọn Lưu để hoàn tất");
      } catch (error) {
        AlertBox.Notify.Failure("Nhập dữ liệu thất bại");
        console.log(error);
      }
    },
    changeFilter: function () {
      this.editForm.model.data.forEach((item) => {
        let match = false;
        let fullname = (item.sv_ho + " " + item.sv_ten).trim().toLowerCase();
        item.show =
          fullname.includes(this.filter.sv_hoten.trim().toLowerCase()) &&
          item.sv_ma
            .trim()
            .toLowerCase()
            .includes(this.filter.sv_ma.trim().toLowerCase());
      });
    },
  },
};
</script>
