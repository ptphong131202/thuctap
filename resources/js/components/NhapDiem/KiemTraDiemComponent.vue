<template>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Kiểm tra dữ liệu nhập
        </h1>
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
            <li class="active">Danh sách môn học</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="box">
                    <div class="box-body">
                        <div class="form-group col-lg-4">
                            <label>Cho phép hssv cập nhật thông tin</label>
                                    <select class="form-control" v-model="selected.configSv">
                                        <option value="0">Không cho phép</option>
                                        <option value="1">Cho phép</option>
                                    </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <button type="submit" class="btn btn-danger" style="margin-top: 25px" v-on:click="processUpdateConfigSv()">
                                <i class="fa fa-save"></i> Thay đổi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      <div class="row">
          <div class="col-md-12 col-sm-12">
              <div class="box">
                  <div class="box-body">
                    <div class="form-group col-lg-6">
                      <label>Lớp học</label>
                      <select class="form-control" v-model="selected.lh_id">
                        <option value="-1">-- Chọn lớp học --</option>
                        <option v-for="lopHoc in danhSachLopHoc" v-bind:value="lopHoc.lh_id" v-bind:key="lopHoc.lh_id">{{ lopHoc.lh_ma }} - {{ lopHoc.lh_ten }}</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6">
                      <label>Học kỳ</label>
                      <select class="form-control" v-model="selected.hk_id">
                        <option value="-1">-- Chọn học kỳ --</option>
                        <option value="1">Học kỳ 1 năm I</option>
                        <option value="2">Học kỳ 2 năm I</option>
                        <option value="3">Học kỳ 1 năm II</option>
                        <option value="4">Học kỳ 2 năm II</option>
                        <option value="5">Học kỳ 1 năm III</option>
                        <option value="6">Học kỳ 2 năm III</option>
                      </select>
                    </div>
                    <div class="clearfix" />
                    <div class="form-group col-lg-6">
                      <label>Tập tin kiểm tra</label>
                      <input type="file" @change="actionSelectFile" />
                    </div>
                    <div class="form-group col-lg-6">
                      <label>Sheet kiểm tra</label>
                      <select class="form-control" v-model="selected.sheet_id">
                        <option value="-1">-- Chọn sheet --</option>
                        <option v-for="(sheet, index) in importSheets" :key="sheet.number" :value="index">{{ sheet.title }}</option>
                      </select>
                    </div>
                    <div class="clearfix" />
                    <div class="col-lg-12">
                      <h4>Tổng lỗi phát hiện: <strong>{{ error.count }}</strong> (trên tổng {{ danhSachSinhVien.length }} học sinh, sinh viên)</h4>
                    </div>
                    <div class="clearfix" />
                    <div class="table-responsive">
                      <table class="table table-striped table-hover table-bordered no-margin " id="table-sinhvien">
                        <thead>
                          <tr>
                            <th style="width: 20px">STT</th>
                            <th style="width: 120px">MSSV</th>
                            <th>Họ & tên</th>
                            <th style="width: 200px;">
                              <select class="form-control" v-model="selected.mh_id" v-if="danhSachMonHoc != null && danhSachMonHoc.length > 0">
                                <option value="-1">-- Chọn môn học --</option>
                                <option v-for="monHoc in danhSachMonHoc" :key="monHoc.mh_id" :value="monHoc.mh_id">{{ monHoc.mh_ten }}</option>
                              </select>
                            </th>
                            <th style="width: 200px;">
                              <select class="form-control" v-model="selected.col_id" v-if="sheet != null">
                                <option value="-1">-- Chọn cột --</option>
                                <option v-for="(col) in sheet.cols" :key="col.index" :value="col.index">{{ col.title }}</option>
                              </select>
                            </th>
                            <th>Ghi chú</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(student, index) in danhSachSinhVien" :key="student.sv_id" v-bind:style="{ background: student.scoreStatusError ? '#f90b0b' : '', color: student.scoreStatusError ? 'white' : '' }">
                            <td class="text-center">{{ index + 1 }}</td>
                            <td class="text-center">{{ student.sv_ma }}</td>
                            <td>{{ student.sv_ho + ' ' + student.sv_ten }}</td>
                            <td style="text-center">
                              {{ student.scoreMonHoc }}
                            </td>
                            <td style="text-center">
                              {{ student.scoreMonHocCheck }}
                            </td>
                            <td>
                              <span>{{ student.scoreStatus }}</span>
                            </td>
                          </tr>
                          <tr v-if="danhSachSinhVien == null || danhSachSinhVien.length == 0">
                            <td colspan="6" class="text-center">
                              Vui lòng nhập dữ liệu
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
              </div>
          </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
  const consumer = {
    updateConfigSV: function (status) {
        const url = `http://localhost/cea_3.0/public/api/cau-hinh/${status}/update-config-allow-info-hssv`; /** Phong API */
        return axios.put(url).then(res => res.data);
    },
    getListLopHoc: function () {
        const url = 'http://localhost/cea_3.0/public/api/lop-hoc/all';/** Phong API */
        return axios.get(url).then(response => response.data);
    },
    getLopHoc: function (lh_id) {
        const url = 'http://localhost/cea_3.0/public/api/lop-hoc/' + lh_id;/** Phong API */
        return axios.get(url).then(response => response.data);
    },
    getKetQuaHocTap: function (lh_id, semester) {
      const url = `http://localhost/cea_3.0/public/api/nhap-diem/${lh_id}/ket-qua-hoc-tap/json?semester=${semester}`;/** Phong API */
      return axios.get(url).then(res => res.data);
    },
    uploadFile: function (file, sheet) {
      var formData = new FormData();
      var headers = {
          headers: {
              'Content-Type': 'multipart/form-data'
          }
      }
      formData.append('excel_file', file);
      // formData.append('sheet', sheet);
      return axios.post('http://localhost/cea_3.0/public/api/excel/import-score-check', formData, headers).then(res => res.data);/** Phong API */
    }
  }

  export default {
    props: ["confighssv"],
    mounted() {
      this.init();
    },
    data() {
        return {
          selected: {
            lh_id: -1,
            hk_id: -1,
            mh_id: -1,
            sheet_id: -1,
            col_id: -1,
            configSv: this.confighssv
          },
          ketQuaHocTap: null,
          lopHoc: null,
          khoaDaoTao: null,
          danhSachSinhVien: [],
          danhSachLopHoc: [],
          danhSachMonHoc: [],
          importSheets: [],
          sheet: null,
          error: {
            count: 0,
          }
        }
    },
    watch: {
      'selected.sheet_id': function(newValue) {
        if (newValue >= 0 && this.importSheets[newValue]) {
          this.sheet = this.importSheets[newValue];
          this.selected.col_id = -1;
          this.processCompare();
        }
      },
      'selected.lh_id': function (newValue) {
        if (newValue > 0) {
          this.selected.hk_id = -1;
          this.selected.mh_id = -1;
          this.loadThongTinLopHoc();
        }
      },
      'selected.hk_id': function (newValue) {
        if (newValue > 0) {
          this.selected.mh_id = -1;
          this.loadThongTinLopHoc();
        }
      },
      'selected.mh_id': function (newValue) {
        if (newValue > 0) {
          this.processCompare();
        }
      },
      'selected.col_id': function (newValue) {
        if (newValue > 0) {
          this.processCompare();
        }
      }
    },
    methods: {
      init: function () {
        consumer.getListLopHoc().then(res => {
          this.danhSachLopHoc = res;
        });
      },
      processUpdateConfigSv: function () {
        const status = this.selected.configSv;
        consumer.updateConfigSV(status)
            .then((response) => {
                console.log("res: ", response);
                AlertBox.Notify.Success('Cập nhật thành công!');

            })
            .catch(error => {
                AlertBox.Notify.Failure('Cập nhật thất bại!');
                console.log(error);
            });
      },
      processCompare: function () {
        var vm = this;

        if (this.selected.mh_id != -1) {
          this.danhSachSinhVien.forEach(function (student) {
            let monHocSelected = student.semesters[0].monHoc.find(mh => mh.mh_id == vm.selected.mh_id);
            if (monHocSelected && monHocSelected.ketQua) {
              student.scoreMonHoc = monHocSelected.ketQua.display_score;
            }
          });
        }

        if (this.selected.col_id != -1 && this.selected.mh_id != -1) {
          this.danhSachSinhVien.forEach(function (student) {
              let sv = vm.sheet.rows.find(sv => sv.sv_ma == student.sv_ma);
              if (sv) {
                let scoreImport = sv.data.find(col => col.index === vm.selected.col_id  );
                if (scoreImport && scoreImport.title != '') {
                  student.scoreMonHocCheck = scoreImport.title;
                } else {
                  student.scoreMonHocCheck = null;
                }
              } else {
                student.scoreStatusError = true;
                student.scoreStatus = 'Không tìm thấy';
              }

              if (sv && student.scoreMonHoc == student.scoreMonHocCheck) {
                student.scoreStatusError = false;
                student.scoreStatus = 'Đúng';
              } else {
                console.log(student.scoreMonHoc);
                console.log(student.scoreMonHocCheck);
                student.scoreStatusError = true;
                student.scoreStatus = 'Chưa chính xác';
              }
            });
        } else {
          this.danhSachSinhVien.forEach(function (student) {
            student.scoreStatusError = false;
            delete student.scoreStatus;
            delete student.scoreMonHocCheck;
          });
        }

        this.error.count = this.danhSachSinhVien.filter(student => student.scoreStatusError).length;
      },
      loadThongTinLopHoc: function () {
        if (this.selected.lh_id > 0 && this.selected.hk_id > 0) {
          this.danhSachMonHoc = [];
          consumer.getKetQuaHocTap(this.selected.lh_id, this.selected.hk_id)
            .then(res => {
              this.ketQuaHocTap = res;
              this.lopHoc = res.lopHoc;
              this.khoaDaoTao = this.lopHoc.khoa_dao_tao;
              this.danhSachSinhVien = res.danhSachSinhVien;
              this.selected.mh_id = -1;
              try {
                this.danhSachMonHoc = this.ketQuaHocTap.danhSachNamHoc[0].semesters[0].monHoc;
              } catch (e) {}
            });
        } else {
          this.danhSachSinhVien = [];
        }
      },
      actionSelectFile: function (event) {
        if (event.target.files.length) {
          consumer.uploadFile(event.target.files[0], null)
            .then((res) => {
                this.importSheets = res;
                this.selected.sheet_id = -1;
            })
            .catch(error => {
                AlertBox.Notify.Failure('Nhập dữ liệu thất bại');
                console.log(error);
            });
        }
      }
    }
  }
</script>
