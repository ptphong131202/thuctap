<template>
  <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header" v-if="editForm.lopHoc != Object">
          <h1>
             Nhật ký nhập điểm lớp {{ editForm.lopHoc.lh_ma }}
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
              <a
                  href="http://localhost/cea-2.0/public/nhat-ky-diem"
                  class="btn btn-default"
              >
                  <i class="fa fa-share"></i> Trở về danh sách
              </a>
          </div>
          <div class="row">
              <div class="col-md-12 col-sm-12">
                  <div class="box box-widget">
                      <div class="box-header with-border">
                          <div class="box-title">
                              {{ editForm.monHoc.mh_ma }} -
                              {{ editForm.monHoc.mh_ten }}
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
                                          v-model="
                                              editForm.model.bd_giangvien
                                          "
                                      />
                                  </div>
                              </div>
                              <div class="col-lg-4 col-md-4">
                                  <div class="form-group">
                                      <label
                                          >Thời gian thực hiện từ ngày</label
                                      >
                                      <!-- <input type="text" class="form-control datepicker" v-model="editForm.model.bd_tungay"/> -->
                                      <div
                                          class="input-group date datetimepicker"
                                      >
                                          <input
                                              id="bd_tungay"
                                              type="text"
                                              class="form-control"
                                              v-model="
                                                  editForm.model
                                                      .bd_tungay_formated
                                              "
                                          />
                                          <span class="input-group-addon">
                                              <i class="fas fa-calendar"></i>
                                          </span>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-lg-4 col-md-4">
                                  <div class="form-group">
                                      <label>Đến ngày</label>
                                      <!-- <input type="text" class="form-control datepicker" v-model="editForm.model.bd_denngay"/> -->
                                      <div
                                          class="input-group date datetimepicker"
                                      >
                                          <input
                                              id="bd_denngay"
                                              type="text"
                                              class="form-control"
                                              v-model="
                                                  editForm.model
                                                      .bd_denngay_formated
                                              "
                                          />
                                          <span class="input-group-addon">
                                              <i class="fas fa-calendar"></i>
                                          </span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div id="panel-diem">
                              <table
                                  class="table table-hover table-striped table-bordered no-margin"
                              >
                                  <thead>
                                      <tr>
                                          <th
                                              rowspan="3"
                                              class="w-3 text-center"
                                          >
                                              #
                                          </th>
                                          <th
                                              rowspan="3"
                                              class="text-center w-5"
                                          >
                                              MSSV
                                              <input
                                                  v-model="filter.sv_ma"
                                                  v-on:keyup="changeFilter"
                                                  type="text"
                                                  class="form-control input-sm"
                                                  placeholder="Tìm kiếm MSSV"
                                              />
                                          </th>
                                          <th
                                              rowspan="3"
                                              colspan="2"
                                              class="text-center"
                                          >
                                              Họ và tên
                                              <input
                                                  v-model="filter.sv_hoten"
                                                  v-on:keyup="changeFilter"
                                                  type="text"
                                                  class="form-control input-sm"
                                                  placeholder="Tìm kiếm theo tên..."
                                              />
                                          </th>
                                          <th
                                              rowspan="3"
                                              class="w-10 text-center"
                                              style="min-width: 85px"
                                          >
                                              Dự lớp (%)
                                          </th>
                                          <th
                                              v-bind:colspan="
                                                  quyChe2022 ? 8 : 3
                                              "
                                              class="w-30 text-center"
                                          >
                                              Điểm
                                          </th>
                                          <th
                                              rowspan="3"
                                              class="w-15 text-center"
                                              style="min-width: 100px"
                                          >
                                              Ghi chú
                                          </th>
                                          <!-- <th colspan="2" class="w-20 text-center" style="min-width: 170px">Ghi chú</th> -->
                                      </tr>
                                      <tr>
                                          <th
                                              v-bind:colspan="
                                                  quyChe2022 ? 2 : 1
                                              "
                                              class="text-center"
                                              :style="
                                                  quyChe2022
                                                      ? 'width: 70px;'
                                                      : ''
                                              "
                                          >
                                              Lần 1
                                          </th>
                                          <th
                                              v-bind:colspan="
                                                  quyChe2022 ? 3 : 2
                                              "
                                              class="text-center"
                                          >
                                              Lần 2
                                          </th>
                                          <th
                                              v-if="quyChe2022"
                                              colspan="3"
                                              class="text-center"
                                          >
                                              Lần 3
                                          </th>
                                      </tr>
                                      <tr>
                                          <!-- Lần 1 -->
                                          <th
                                              v-if="quyChe2022"
                                              class="text-center"
                                              :style="
                                                  quyChe2022
                                                      ? 'width: 70px;'
                                                      : ''
                                              "
                                          >
                                              Điểm thi
                                          </th>
                                          <th
                                              class="text-center"
                                              :style="
                                                  quyChe2022
                                                      ? 'width: 70px;'
                                                      : ''
                                              "
                                          >
                                              Điểm TK
                                          </th>
                                          <!-- Lần 2 -->
                                          <th
                                              v-if="quyChe2022"
                                              class="text-center"
                                              style="min-width: 70px"
                                          >
                                              Điểm thi
                                          </th>
                                          <th
                                              class="text-center"
                                              style="min-width: 70px"
                                          >
                                              Điểm TK
                                          </th>
                                          <th
                                              class="text-center"
                                              style="min-width: 120px"
                                          >
                                              Thời điểm cải thiện
                                          </th>
                                          <!-- Lần 3 -->
                                          <th
                                              v-if="quyChe2022"
                                              class="text-center"
                                              style="min-width: 70px"
                                          >
                                              Điểm thi
                                          </th>
                                          <th
                                              v-if="quyChe2022"
                                              class="text-center"
                                              style="min-width: 70px"
                                          >
                                              Điểm TK
                                          </th>
                                          <th
                                              v-if="quyChe2022"
                                              class="text-center"
                                              style="min-width: 120px"
                                          >
                                              Thời điểm cải thiện
                                          </th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <template
                                          v-for="(sv, indexSv) in editForm
                                              .model.data"
                                      >
                                          <tr
                                              :key="sv.sv_id"
                                              v-show="sv.show"
                                          >
                                              <td class="text-center">
                                                  {{ indexSv + 1 }}
                                              </td>
                                              <td class="text-center">
                                                  {{ sv.sv_ma }}
                                              </td>
                                              <td class="w-10">
                                                  {{ sv.sv_ho }}
                                              </td>
                                              <td class="w-10">
                                                  {{ sv.sv_ten }}
                                              </td>
                                              <td>
                                                  <span
                                                      @click="
                                                          showLog(sv.sv_id)
                                                      "
                                                      class="form-control"
                                                      v-if="
                                                          check(
                                                              sv,
                                                              'svd_dulop'
                                                          )
                                                      "
                                                      style="color: red"
                                                  >
                                                      <i>*</i>
                                                      {{ sv.svd_dulop }}</span
                                                  >
                                                  <span
                                                      v-else
                                                      class="form-control"
                                                  >
                                                      {{ sv.svd_dulop }}
                                                  </span>
                                              </td>
                                              <td v-if="quyChe2022">
                                                  <div
                                                      style="
                                                          min-width: 80px;
                                                          text-align: center;
                                                      "
                                                  >
                                                      <span
                                                          @click="
                                                              showLog(
                                                                  sv.sv_id
                                                              )
                                                          "
                                                          class="form-control"
                                                          v-if="
                                                              check(
                                                                  sv,
                                                                  'svd_exam_first'
                                                              )
                                                          "
                                                          style="color: red"
                                                      >
                                                          <i>*</i>
                                                          {{
                                                              sv.svd_exam_first
                                                          }}</span
                                                      >
                                                      <span
                                                          v-else
                                                          class="form-control"
                                                      >
                                                          {{
                                                              sv.svd_exam_first
                                                          }}
                                                      </span>
                                                  </div>
                                              </td>
                                              <td>
                                                  <div
                                                      style="min-width: 80px; text-align: center;"
                                                  >
                                                  <span
                                                          @click="
                                                              showLog(
                                                                  sv.sv_id
                                                              )
                                                          "
                                                          class="form-control"
                                                          v-if="
                                                              check(
                                                                  sv,
                                                                  'svd_first'
                                                              )
                                                          "
                                                          style="color: red"
                                                      >
                                                          <i>*</i>
                                                          {{
                                                              sv.svd_first
                                                          }}</span
                                                      >
                                                      <span
                                                          v-else
                                                          class="form-control"
                                                      >
                                                          {{
                                                              sv.svd_first
                                                          }}
                                                      </span>
                                                  </div>
                                              </td>
                                              <td v-if="quyChe2022">
                                                <span
                                                          @click="
                                                              showLog(
                                                                  sv.sv_id
                                                              )
                                                          "
                                                          class="form-control"
                                                          v-if="
                                                              check(
                                                                  sv,
                                                                  'svd_exam_second'
                                                              )
                                                          "
                                                          style="color: red"
                                                      >
                                                          <i>*</i>
                                                          {{
                                                              sv.svd_exam_second
                                                          }}</span
                                                      >
                                                      <span
                                                          v-else
                                                          class="form-control"
                                                      >
                                                          {{
                                                              sv.svd_exam_second
                                                          }}
                                                      </span>
                                              </td>
                                              <td>
                                                <span
                                                          @click="
                                                              showLog(
                                                                  sv.sv_id
                                                              )
                                                          "
                                                          class="form-control"
                                                          v-if="
                                                              check(
                                                                  sv,
                                                                  'svd_second'
                                                              )
                                                          "
                                                          style="color: red"
                                                      >
                                                          <i>*</i>
                                                          {{
                                                              sv.svd_second
                                                          }}</span
                                                      >
                                                      <span
                                                          v-else
                                                          class="form-control"
                                                      >
                                                          {{
                                                              sv.svd_second
                                                          }}
                                                      </span>
                                              </td>
                                              <td>
                                                  
                                                  <span
                                                          @click="
                                                              showLog(
                                                                  sv.sv_id
                                                              )
                                                          "
                                                          class="form-control" 
                                                          v-if="
                                                              check(
                                                                  sv,
                                                                  'svd_second_hocky'
                                                              )
                                                          "
                                                          style="color: red; width: 120px;  text-align: center;"
                                                      >
                                                          <i>*</i>
                                                          <span v-if="sv.svd_second_hocky === 1">Năm I, HK 1</span>
                                                          <span v-if="sv.svd_second_hocky === 2">Năm I, HK 2</span>
                                                          <span v-if="sv.svd_second_hocky === 3">Năm II, HK 1</span>
                                                          <span v-if="sv.svd_second_hocky === 4">Năm II, HK 2</span>
                                                          <span v-if="sv.svd_second_hocky === 5">Năm III, HK 1</span>
                                                          <span v-if="sv.svd_second_hocky === 6">Năm III, HK 2</span>
                                                          </span
                                                      >
                                                      <span
                                                          v-else
                                                          class="form-control"
                                                      >
                                                      <span style=" width: 120px;  text-align: center;" v-if="sv.svd_second_hocky === 1">Năm I, HK 1</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_second_hocky === 2">Năm I, HK 2</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_second_hocky === 3">Năm II, HK 1</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_second_hocky === 4">Năm II, HK 2</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_second_hocky === 5">Năm III, HK 1</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_second_hocky === 6">Năm III, HK 2</span>
                                                      </span>
                                              </td>
                                              <td v-if="quyChe2022">
                                                <span
                                                          @click="
                                                              showLog(
                                                                  sv.sv_id
                                                              )
                                                          "
                                                          class="form-control"
                                                          v-if="
                                                              check(
                                                                  sv,
                                                                  'svd_exam_third'
                                                              )
                                                          "
                                                          style="color: red"
                                                      >
                                                          <i>*</i>
                                                          {{
                                                              sv.svd_exam_third
                                                          }}</span
                                                      >
                                                      <span
                                                          v-else
                                                          class="form-control"
                                                      >
                                                          {{
                                                              sv.svd_exam_third
                                                          }}
                                                      </span>
                                              </td>
                                              <td v-if="quyChe2022">
                                                  <span
                                                          @click="
                                                              showLog(
                                                                  sv.sv_id
                                                              )
                                                          "
                                                          class="form-control"
                                                          v-if="
                                                              check(
                                                                  sv,
                                                                  'svd_third'
                                                              )
                                                          "
                                                          style="color: red"
                                                      >
                                                          <i>*</i>
                                                          {{
                                                              sv.svd_third
                                                          }}</span
                                                      >
                                                      <span
                                                          v-else
                                                          class="form-control"
                                                      >
                                                          {{
                                                              sv.svd_third
                                                          }}
                                                      </span>
                                              </td>
                                              <td v-if="quyChe2022">
                                                <span
                                                          @click="
                                                              showLog(
                                                                  sv.sv_id
                                                              )
                                                          "
                                                          class="form-control" 
                                                          v-if="
                                                              check(
                                                                  sv,
                                                                  'svd_third_hocky'
                                                              )
                                                          "
                                                          style="color: red ; width: 120px;  text-align: center;"
                                                      >
                                                          <i>*</i>
                                                          <span v-if="sv.svd_third_hocky === 1">Năm I, HK 1</span>
                                                          <span v-if="sv.svd_third_hocky === 2">Năm I, HK 2</span>
                                                          <span v-if="sv.svd_third_hocky === 3">Năm II, HK 1</span>
                                                          <span v-if="sv.svd_third_hocky === 4">Năm II, HK 2</span>
                                                          <span v-if="sv.svd_third_hocky === 5">Năm III, HK 1</span>
                                                          <span v-if="sv.svd_third_hocky === 6">Năm III, HK 2</span>
                                                          </span
                                                      >
                                                      <span
                                                          v-else
                                                          class="form-control"
                                                      >
                                                      <span style=" width: 120px;  text-align: center;" v-if="sv.svd_third_hocky === 1">Năm I, HK 1</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_third_hocky === 2">Năm I, HK 2</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_third_hocky === 3">Năm II, HK 1</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_third_hocky === 4">Năm II, HK 2</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_third_hocky === 5">Năm III, HK 1</span>
                                                          <span style=" width: 120px;  text-align: center;" v-if="sv.svd_third_hocky === 6">Năm III, HK 2</span>
                                                      </span>
                                              </td>
                                              <td>
                                                  <input
                                                      type="text"
                                                      v-model="sv.svd_ghichu"
                                                      class="form-control"
                                                  />
                                              </td>
                                          </tr>
                                          <tr
                                              style="
                                                  background-color: #e4d080;
                                              "
                                              v-for="index in sv.nhatkysua"
                                              :key="index"
                                              v-if="showId===sv.sv_id && checknull(index) && checkCompare(index, sv.nhatkysua)" 

                                          >
                                              <td
                                                  colspan="2"
                                                  class="text-center"
                                              >
                                                  {{ index.thoigian }}
                                              </td>

                                              <td
                                                  colspan="2"
                                                  class="text-center"
                                              >
                                                  {{ index.cb_ten }}
                                              </td>
                                              <td>
                                                  <span class="form-control">
                                                      {{ index.svd_dulop }}
                                                  </span>
                                              </td>
                                              <td v-if="quyChe2022">
                                                  <div
                                                      style="
                                                          min-width: 80px;
                                                          text-align: center;
                                                      "
                                                  >
                                                      <span
                                                          class="form-control"
                                                          >{{
                                                              index.svd_exam_first
                                                          }}
                                                      </span>
                                                  </div>
                                              </td>
                                              <td>
                                                  <div
                                                      style="min-width: 80px; text-align: center;"
                                                  >
                                                      <input
                                                          type="number"
                                                          v-model="
                                                              index.svd_first
                                                          "
                                                          class="form-control"
                                                      />
                                                  </div>
                                              </td>
                                              <td v-if="quyChe2022">
                                                  <input
                                                      type="number"
                                                      v-model="
                                                          index.svd_exam_second
                                                      "
                                                      class="form-control"
                                                  />
                                              </td>
                                              <td>
                                                  <input
                                                      type="number"
                                                      v-model="
                                                          index.svd_second
                                                      "
                                                      class="form-control"
                                                  />
                                              </td>
                                              <td>
                                                  <select
                                                      class="form-control"
                                                      v-model="
                                                          index.svd_second_hocky
                                                      "
                                                  >
                                                      <option value="1">
                                                          Năm I, HK 1
                                                      </option>
                                                      <option value="2">
                                                          Năm I, HK 2
                                                      </option>
                                                      <option value="3">
                                                          Năm II, HK 1
                                                      </option>
                                                      <option value="4">
                                                          Năm II, HK 2
                                                      </option>
                                                      <option value="5">
                                                          Năm III, HK 1
                                                      </option>
                                                      <option value="6">
                                                          Năm III, HK 2
                                                      </option>
                                                  </select>
                                              </td>
                                              <td v-if="quyChe2022">
                                                  <input
                                                      type="number"
                                                      v-model="
                                                          index.svd_exam_third
                                                      "
                                                      class="form-control"
                                                  />
                                              </td>
                                              <td v-if="quyChe2022">
                                                  <input
                                                      type="number"
                                                      v-model="
                                                          index.svd_third
                                                      "
                                                      class="form-control"
                                                  />
                                              </td>
                                              <td v-if="quyChe2022">
                                                  <select
                                                      class="form-control"
                                                      v-model="
                                                          index.svd_third_hocky
                                                      "
                                                  >
                                                      <option value="1">
                                                          Năm I, HK 1
                                                      </option>
                                                      <option value="2">
                                                          Năm I, HK 2
                                                      </option>
                                                      <option value="3">
                                                          Năm II, HK 1
                                                      </option>
                                                      <option value="4">
                                                          Năm II, HK 2
                                                      </option>
                                                      <option value="5">
                                                          Năm III, HK 1
                                                      </option>
                                                      <option value="6">
                                                          Năm III, HK 2
                                                      </option>
                                                  </select>
                                              </td>
                                              <td>
                                                <span class="form-controll">{{ index.svd_ghichu }}</span>
                                              </td>
                                          </tr>
                                      </template>

                                      <tr
                                          v-if="
                                              editForm.model.data == null ||
                                              editForm.model.data == 0
                                          "
                                      >
                                          <td
                                              colspan="100"
                                              class="text-center"
                                          >
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
                          <a
                              href="http://localhost/cea-2.0/public/nhat-ky-diem"
                              class="btn btn-default"
                          >
                              <i class="fa fa-share"></i> Trở về danh sách
                          </a>
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
  getLopHoc: function (lh_id) {
      const url = "http://localhost/cea-2.0/public/api/lop-hoc/" + lh_id;
      return axios.get(url).then((response) => response.data);
  },
  getMonHoc: function (mh_id) {
      const url = "http://localhost/cea-2.0/public/api/mon-hoc/" + mh_id;
      return axios.get(url).then((response) => response.data);
  },
  getDanhSachHocKy: function (kdt_id) {
      const url =
          "http://localhost/cea-2.0/public/api/khoa-dao-tao/" +
          kdt_id +
          "/hoc-ky";
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
                  semester.monHoc.sort(
                      (a, b) => a.pivot.kdt_mh_index - b.pivot.kdt_mh_index
                  );
              });
              return data;
          });
  },
  getBangDiem: function (bd_id, thoigian) {
      // bd_type = 1 là bảng điểm môn học
      const url = `http://localhost/cea-2.0/public/api/nhap-diem/bang-diem-log?bd_id=${bd_id}&thoigian="${thoigian}"`;
      return axios.get(url).then((response) => response.data);
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
  props: ["bd_id", "thoigian", "lh_id", "mh_id"],
  mounted() {
      this.loadLopHoc();
      this.reloadSinhVien();
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
          showId: "",
          shownotcompare: false,
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
    checkCompare(item, svList) {
            let index = 0;
            for (let i = 0; i < svList.length; i++) {
                if (item.thoigian == svList[i].thoigian) index = i;
            }
            if (index > 0) {
                if (
                    item.svd_dulop === svList[index - 1].svd_dulop &&
                    item.svd_first === svList[index - 1].svd_first &&
                    item.svd_second === svList[index - 1].svd_second &&
                    item.svd_third === svList[index - 1].svd_third &&
                    item.svd_exam_first === svList[index - 1].svd_exam_first &&
                    item.svd_exam_second === svList[index - 1].svd_exam_second &&
                    item.svd_ghichu === svList[index - 1].svd_ghichu &&
                    item.svd_exam_third === svList[index - 1].svd_exam_third 

                ) return false;
            }
            return true;
        }        ,
    checknull(sv) {
        return sv.svd_dulop === null && sv.svd_first === null ? false : true;
     },
      showLog(sv_id) {
          if (this.showId == sv_id) this.showId = "";
          else this.showId = sv_id;
      },
      check(sv, name) {
          for (let element of sv.nhatkysua) 
                  return element[name] !== sv[name];
          
      },
      fixEditDate: function () {
          $(".datetimepicker").datetimepicker({
              format: "DD/MM/YYYY",
          });
      },
      loadLopHoc: function () {
          var vm = this;
          // Load thông tin lớp học
          consumer.getLopHoc(this.lh_id).then((lopHoc) => {
              console.log(this.lh_id);
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
          panelDiem.block();
          consumer.getBangDiem(this.bd_id, this.thoigian).then((data) => {
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

              let dateOptions = {
                  year: "numeric",
                  month: "2-digit",
                  day: "2-digit",
              };
              if (data.bd_tungay) {
                  data.bd_tungay_formated = moment(
                      data.bd_tungay,
                      "YYYY-MM-DD"
                  ).format("DD/MM/YYYY");
              }

              if (data.bd_denngay) {
                  data.bd_denngay_formated = moment(
                      data.bd_denngay,
                      "YYYY-MM-DD"
                  ).format("DD/MM/YYYY");
              }

              this.editForm.model = data;
              panelDiem.unblock();
          });
      },

      changeFilter: function () {
          this.editForm.model.data.forEach((item) => {
              let match = false;
              let fullname = (item.sv_ho + " " + item.sv_ten)
                  .trim()
                  .toLowerCase();
              item.show =
                  fullname.includes(
                      this.filter.sv_hoten.trim().toLowerCase()
                  ) &&
                  item.sv_ma
                      .trim()
                      .toLowerCase()
                      .includes(this.filter.sv_ma.trim().toLowerCase());
          });
      },
  },
};
</script>
