<template>
  <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
          <h1>
              Quản lý lớp học
              <button class="btn btn-danger btn-flat btn-xs">
                <a style="color: white;" href="javascript:void(0);"  title="Thêm mới" v-on:click="create()">
                <i class="fa fa-plus"></i> Thêm mới
                </a>
                </button>
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
          <div class="row">
              <div class="col-md-12 col-sm-12">
                  <div class="box">
                      <div class="box-body">
                          <form method="get">
                              <div class="row">
                                  <div class="col-md-4">
                                      <div class="form-group">
                                          <label>Mã hoặc tên lớp</label>
                                          <input
                                              type="text"
                                              name="search"
                                              v-model="filter.search"
                                              class="form-control"
                                          />
                                      </div>
                                  </div>
                                  <div class="col-md-2">
                                      <div class="form-group">
                                          <label>Niên khóa</label>
                                          <select
                                              name="nienkhoa"
                                              class="form-control"
                                              v-model="filter.nienkhoa"
                                          >
                                              <option value="-1">
                                                  Tất cả
                                              </option>
                                              <option
                                                  v-for="nk in listNienKhoa.select2"
                                                  :key="nk.id"
                                                  v-bind:value="nk.id"
                                              >
                                                  {{ nk.text }}
                                              </option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-md-4">
                                      <div class="form-group">
                                          <label>Hệ đào tạo</label>
                                          <select
                                              name="chuongtrinh"
                                              class="form-control"
                                              v-model="filter.chuongtrinh"
                                          >
                                              <option
                                                  v-for="nk in listHeDaoTao.select2"
                                                  :key="nk.id"
                                                  v-bind:value="nk.id"
                                              >
                                                  {{ nk.text }}
                                              </option>
                                          </select>
                                      </div>
                                  </div>
                                  <div
                                      class="col-md-2"
                                      style="margin-top: 23px"
                                  >
                                      <button
                                          type="submit"
                                          class="btn btn-default"
                                      >
                                          <i class="fa fa-search"></i> Tìm
                                          kiếm
                                      </button>
                                  </div>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
              <div class="col-md-12 col-sm-12">
                  <div class="box box-widget">
                      <div class="box-body table-responsive">
                          <table
                              class="table table-striped table-bordered no-margin"
                          >
                              <thead>
                                  <tr>
                                      <th class="w-3 text-center">#</th>
                                      <th class="w-10">Mã lớp</th>
                                      <th>Lớp học</th>
                                      <th>Khóa</th>
                                      <th class="w-10">Niên khóa</th>
                                      <th class="w-13">
                                          Chương trình đào tạo
                                      </th>
                                      <th>Quyết định</th>
                                      <th>Quy chế</th>
                                      <th class="w-4">Sinh viên</th>
                                      <th class="w-12 text-center">
                                          Hành động
                                      </th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr
                                      v-for="(lh, index) in table.list.data"
                                      :key="lh.lh_id"
                                  >
                                      <td class="text-center">
                                          {{ index + 1 }}
                                      </td>
                                      <td>{{ lh.lh_ma }}</td>
                                      <td>
                                          {{ lh.lh_ten }}
                                          <!-- <a
                      type="button"
                      class="btn-sm pull-right"
                      title="Sao chép"
                      style="color: black; cursor: pointer"
                      v-on:click="preActionDuplicate(lh.lh_id)"
                      ><i class="fa fa-copy"></i>
                    </a> -->
                                      </td>
                                      <td>{{ lh.khoa_dao_tao.kdt_khoa }}</td>
                                      <td>{{ lh.nien_khoa.nk_ten }}</td>
                                      <td>{{ lh.khoa_dao_tao.kdt_ten }}</td>
                                      <td>
                                          <span
                                              v-if="lh.quyet_dinh"
                                              :title="lh.quyet_dinh.qd_ten"
                                              >{{ lh.quyet_dinh.qd_ma }},
                                              {{
                                                  lh.quyet_dinh.qd_ngay
                                                      | moment
                                              }}</span
                                          >
                                      </td>
                                      <td>
                                          <span v-if="lh.lh_nienche == 0"
                                              >Quy chế 2020</span
                                          >
                                          <span v-if="lh.lh_nienche == 1"
                                              >Quy chế 2022</span
                                          >
                                      </td>

                                      <!-- P.Dinh  -->
                                      <td>
                                          <a
                                              :href="lh.edit_url"
                                              class="btn bg-purple btn-flat btn-sm btn-block"
                                          >
                                              <i class="fa fa-users"></i>
                                              {{
                                                  lh.sinh_vien_hien_tai > 9
                                                      ? lh.sinh_vien_hien_tai
                                                      : "0" +
                                                        lh.sinh_vien_hien_tai
                                              }}/
                                              {{
                                                  lh.sinh_vien_count > 9
                                                      ? lh.sinh_vien_count
                                                      : "0" +
                                                        lh.sinh_vien_count
                                              }}
                                              sinh viên
                                          </a>
                                      </td>
                                      <!-- P.Dinh  -->
                                      <td class="text-left">
                                          <a
                                              :href="lh.edit_monhoc_url"
                                              class="btn btn-success btn-sm bg-purple"
                                              title="Quản lý học kỳ"
                                          >
                                              <i class="fa fa-cogs"></i>
                                          </a>
                                          <!-- <a
                      :href="lh.importexcel_url"
                      class="btn btn-success btn-sm"
                      title="Thêm sinh viên bằng excel"
                    >
                      <i class="fa fa-file-excel-o"></i>
                    </a> -->
                                          <button
                                              type="button"
                                              title="Thay đổi"
                                              class="btn bg-orange btn-sm"
                                              v-on:click="edit(lh.lh_id)"
                                          >
                                              <i class="fa fa-edit"></i>
                                          </button>
                                          <button
                                              type="button"
                                              title="Xóa"
                                              class="btn btn-danger btn-sm"
                                              v-if="lh.sinh_vien_count == 0"
                                              v-on:click="destroy(lh.lh_id)"
                                          >
                                              <i class="fa fa-trash"></i>
                                          </button>

                                          <button
                                              type="button"
                                              class="btn btn-info btn-sm"
                                              title="Sao chép lớp học"
                                              style="
                                                  color: pointer;
                                                  cursor: pointer;
                                              "
                                              v-on:click="
                                                  preActionDuplicate(lh.lh_id)
                                              "
                                          >
                                              <i class="fa fa-copy"></i>
                                          </button>
                                      </td>
                                  </tr>
                                  <tr
                                      v-if="
                                          table.list.data == null ||
                                          table.list.data.length == 0
                                      "
                                  >
                                      <td colspan="100" class="text-center">
                                          {{
                                              table.list.data != null
                                                  ? "Không tìm thấy dữ liệu"
                                                  : "Đang tải"
                                          }}
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

      <div class="modal fade" id="lop-hoc-edit-modal">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <button
                          type="button"
                          class="close"
                          data-dismiss="modal"
                          aria-label="Close"
                      >
                          <span aria-hidden="true">&times;</span>
                      </button>
                      <h4 class="modal-title">
                          {{
                              editForm.reference == Object
                                  ? "Thêm Lớp học"
                                  : editForm.reference.lh_ten
                          }}
                      </h4>
                  </div>
                  <div class="modal-body">
                      <form-group :errors="editForm.errors" :field="'kdt_id'">
                          <label>Chương trình đào tạo</label>
                          <select2
                              v-model="editForm.model.kdt_id"
                              :options="listKhoaDaoTao.select2"
                          ></select2>
                      </form-group>
                      <form-group :errors="editForm.errors" :field="'lh_ma'">
                          <label>Mã lớp</label>
                          <input
                              type="text"
                              class="form-control"
                              v-model="editForm.model.lh_ma"
                          />
                      </form-group>
                      <form-group :errors="editForm.errors" :field="'lh_ten'">
                          <label>Tên lớp</label>
                          <input
                              type="text"
                              class="form-control"
                              v-model="editForm.model.lh_ten"
                          />
                      </form-group>
                      <form-group
                          :errors="editForm.errors"
                          :field="'lh_nienche'"
                      >
                          <label>Quy chế</label>
                          <select
                              class="form-control"
                              v-model="editForm.model.lh_nienche"
                          >
                              <option value="0">Quy chế 2020</option>
                              <option value="1">Quy chế 2022</option>
                          </select>
                      </form-group>
                      <form-group :errors="editForm.errors" :field="'nk_id'">
                          <label>Niên Khóa</label>
                          <select2
                              v-model="editForm.model.nk_id"
                              :options="listNienKhoa.select2"
                          ></select2>
                      </form-group>
                      <!-- <form-group :errors="editForm.errors" :field="'lh_ghichu'">
                            <label>Ghi chú</label>
                            <textarea v-model="editForm.model.lh_ghichu" class="form-control" />
                        </form-group> -->
                      <form-group :errors="editForm.errors" :field="'qd_id'">
                          <label>Thành lập theo quyết định</label>
                          <select2
                              v-model="editForm.model.qd_id"
                              :options="listQuyetDinh.select2"
                          ></select2>
                      </form-group>
                      <div class="row" v-if="editForm.model.qd_id == 0">
                          <div class="col-md-6">
                              <form-group
                                  :errors="editForm.errors"
                                  :field="'qd_ma'"
                              >
                                  <label>Số quyết định</label>
                                  <input
                                      type="text"
                                      v-model="editForm.model.qd_ma"
                                      class="form-control"
                                  />
                              </form-group>
                          </div>
                          <div class="col-md-6">
                              <form-group
                                  :errors="editForm.errors"
                                  :field="'qd_ngay'"
                              >
                                  <label>Ngày quyết định</label>
                                  <input
                                      type="date"
                                      v-model="editForm.model.qd_ngay"
                                      class="form-control"
                                  />
                              </form-group>
                          </div>
                          <div class="col-md-12">
                              <form-group
                                  :errors="editForm.errors"
                                  :field="'qd_ten'"
                              >
                                  <label>Tên quyết định</label>
                                  <textarea
                                      v-model="editForm.model.qd_ten"
                                      class="form-control"
                                  />
                              </form-group>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button
                          type="button"
                          class="btn btn-default"
                          data-dismiss="modal"
                      >
                          <i class="fa fa-times-circle-o"></i> Hủy bỏ
                      </button>
                      <button
                          type="button"
                          class="btn bg-purple"
                          v-on:click="save"
                      >
                          <i class="fa fa-save"></i> Lưu
                      </button>
                  </div>
              </div>
          </div>
      </div>

      <div class="modal fade" id="duplicate-khoa-dao-tao-modal">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <button
                          type="button"
                          class="close"
                          data-dismiss="modal"
                          aria-label="Close"
                      >
                          <span aria-hidden="true">&times;</span>
                      </button>
                      <h4 class="modal-title">Sao chép lớp học</h4>
                  </div>
                  <div class="modal-body">
                      <form-group :errors="editForm.errors" :field="'nn_id'">
                          <label>Sao chép</label>
                          <div>
                              <span
                                  >{{ editForm.reference.lh_ma }} -
                                  {{ editForm.reference.lh_ten }}</span
                              >
                          </div>
                      </form-group>
                      <form-group :errors="editForm.errors" :field="'lh_ma'">
                          <label>Mã lớp học mới</label>
                          <input
                              type="text"
                              class="form-control"
                              v-model="editForm.model.lh_ma"
                          />
                      </form-group>
                      <form-group :errors="editForm.errors" :field="'lh_ten'">
                          <label>Tên lớp học mới</label>
                          <input
                              type="text"
                              class="form-control"
                              v-model="editForm.model.lh_ten"
                          />
                      </form-group>
                      <div class="callout callout-info">
                          <h4>Lưu ý!</h4>

                          <p>
                              Chỉ có thể sao chép lớp học cùng hệ đào tạo và
                              cùng ngành nghề đào tạo
                          </p>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button
                          type="button"
                          class="btn btn-default"
                          data-dismiss="modal"
                      >
                          <i class="fa fa-times-circle-o"></i> Hủy bỏ
                      </button>
                      <button
                          type="button"
                          class="btn bg-purple"
                          v-on:click="duplicate"
                      >
                          <i class="fa fa-save"></i> Lưu
                      </button>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
// import Label from '../../../../vendor/laravel/breeze/stubs/inertia-vue/resources/js/Components/Label.vue';
const editModal = {
  modal: function () {
      return $("#lop-hoc-edit-modal");
  },
  show: function () {
      this.modal().modal({ keyboard: false, backdrop: "static" });
  },
  hide: function () {
      this.modal().modal("hide");
  },
};

const consumer = {
  getListLopHoc: function () {
      const url =
          "http://localhost/cea-3.0/public/api/lop-hoc/paginate" +
          window.location.search;
      return axios.get(url).then((response) => response.data);
  },
  getLopHoc: function (lh_id) {
      const url = "http://localhost/cea-3.0/public/api/lop-hoc/" + lh_id;
      return axios.get(url).then((response) => response.data);
  },
  saveOrUpdate: function (formData) {
      if (formData.lh_id == null) {
          return axios.post("/api/lop-hoc", formData);
      } else {
          return axios.put("/api/lop-hoc/" + formData.lh_id, formData);
      }
  },
  destroy: function (lh_id) {
      return axios.delete("/api/lop-hoc/" + lh_id);
  },
  getListKhoaDaoTao: function (hdt_id) {
      const url =
          "http://localhost/cea-3.0/public/api/khoa-dao-tao/all?hedaotao=" +
          hdt_id;
      return axios.get(url).then((response) => response.data);
  },
  getListQuyetDinh: function () {
      const url = "http://localhost/cea-3.0/public/api/quyet-dinh/all/0";
      return axios.get(url).then((response) => response.data);
  },
  getListNienKhoa: function () {
      const url = "http://localhost/cea-3.0/public/api/nien-khoa/all";
      return axios.get(url).then((response) => response.data);
  },
  getListHeDaoTao: function () {
      const url = "http://localhost/cea-3.0/public/api/he-dao-tao/all";
      return axios.get(url).then((response) => response.data);
  },
  duplicate: function (formData) {
      return axios.post("/api/lop-hoc/duplicate/" + formData.lh_id, formData);
  },
};

const duplicateModal = {
  modal: function () {
      return $("#duplicate-khoa-dao-tao-modal");
  },
  show: function () {
      this.modal().modal({ keyboard: false, backdrop: "static" });
  },
  hide: function () {
      this.modal().modal("hide");
  },
};

const store = {
  table: {
      set list(data) {
          window.localStorage.setItem("lh.table.list", JSON.stringify(data));
      },
      get list() {
          return window.localStorage.getItem("lh.table.list")
              ? JSON.parse(window.localStorage.getItem("lh.table.list"))
              : {};
      },
  },
};

export default {
  props: ["permissions"],
  mounted() {
      // this.quyenTrungCap = this.permissions.includes("trungcap");
      //  this.quyenCaoDang = this.permissions.includes("caodang");
      if (
          new URLSearchParams(window.location.search).get("chuongtrinh") !=
          null
      ) {
          this.filter.chuongtrinh = new URLSearchParams(
              window.location.search
          ).get("chuongtrinh");
      } else if (
          new URLSearchParams(window.location.search).get("chuongtrinh") ==
              null &&
          this.quyenCaoDang &&
          this.quyenTrungCap
      ) {
          this.filter.chuongtrinh = -1;
      } else if (this.quyenCaoDang) {
          this.filter.chuongtrinh = 4;
      } else {
          this.filter.chuongtrinh = 5;
      }
      this.reloadList();
      this.reloadListDm();
  },
  data() {
      return {
          quyenCaoDang: true,
          quyenTrungCap: true,
          filter: {
              search: new URLSearchParams(window.location.search).get(
                  "search"
              ),
              nienkhoa:
                  new URLSearchParams(window.location.search).get(
                      "nienkhoa"
                  ) == null
                      ? -1
                      : new URLSearchParams(window.location.search).get(
                            "nienkhoa"
                        ),
              chuongtrinh:
                  new URLSearchParams(window.location.search).get(
                      "chuongtrinh"
                  ) == null
                      ? -1
                      : new URLSearchParams(window.location.search).get(
                            "chuongtrinh"
                        ),
          },
          editForm: {
              reference: Object,
              model: Object,
              errors: Object,
          },
          table: {
              list: store.table.list,
          },
          listNienKhoa: {
              select2: [],
          },
          listKhoaDaoTao: {
              select2: [],
          },
          listHeDaoTao: {
              select2: [],
          },
          listQuyetDinh: {
              select2: [],
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
      reloadList: function () {
          var vm = this;
          consumer.getListLopHoc().then((data) => {
              Vue.set(vm.table, "list", data);
              store.table.list = data;
          });
      },
      reloadListDm() {
          var vm = this;
          if (vm.listNienKhoa.select2.length == 0) {
              consumer.getListNienKhoa().then((data) => {
                  vm.listNienKhoa.select2 = data.map((item) => {
                      return {
                          id: item.nk_id,
                          text: item.nk_ten,
                      };
                  });
              });
          }

          if (vm.listKhoaDaoTao.select2.length == 0) {
              consumer.getListKhoaDaoTao().then((data) => {
                  vm.listKhoaDaoTao.select2 = data
                      .filter((item) => {
                          return (
                              (item.he_dao_tao.hdt_ten.toLowerCase() ==
                                  "cao đẳng" &&
                                  this.quyenCaoDang) ||
                              (item.he_dao_tao.hdt_ten.toLowerCase() ==
                                  "trung cấp" &&
                                  this.quyenTrungCap)
                          );
                      })
                      .map((item) => {
                          return {
                              id: item.kdt_id,
                              text: item.kdt_ten,
                          };
                      });
              });
          }

          if (vm.listQuyetDinh.select2.length == 0) {
              consumer.getListQuyetDinh().then((data) => {
                  vm.listQuyetDinh.select2.push({
                      id: -1,
                      text: "Không chọn",
                  });
                  vm.listQuyetDinh.select2.push({
                      id: 0,
                      text: "Quyết định mới",
                  });
                  vm.listQuyetDinh.select2.push(
                      ...data.map((item) => {
                          const [year, month, day] = item.qd_ngay.split("-");
                          // Concatenate them in the desired format
                          const formattedDate = `${day}/${month}/${year}`;
                          const name =
                              "QĐ số " +
                              item.qd_ma +
                              " ngày " +
                              formattedDate +
                              " về việc " +
                              item.qd_ten;
                          return {
                              id: item.qd_id,
                              text: name,
                          };
                      })
                  );
              });
          }

          if (vm.listHeDaoTao.select2.length == 0) {
              consumer.getListHeDaoTao().then((data) => {
                  vm.listHeDaoTao.select2 = data
                      .filter((item) => {
                          return (
                              (item.hdt_ten.toLowerCase() == "cao đẳng" &&
                                  this.quyenCaoDang) ||
                              (item.hdt_ten.toLowerCase() == "trung cấp" &&
                                  this.quyenTrungCap)
                          );
                      })
                      .map((item) => {
                          return {
                              id: item.hdt_id,
                              text: item.hdt_ten,
                          };
                      });

                  if (this.quyenCaoDang && this.quyenTrungCap) {
                      vm.listHeDaoTao.select2.unshift({
                          id: -1,
                          text: "Tất cả",
                          selected: true,
                      });
                  }
              });
          }
      },
      resetEditForm: function () {
          this.editForm = {
              reference: Object,
              model: {
                  qd_loai: 0, //quyết định thêm lớp
                  qd_id: -1,
                  lh_nienche: 0,
              },
          };
          if (this.listNienKhoa.select2.length > 0) {
              this.editForm.model.nk_id = this.listNienKhoa.select2[0].id;
          }
          if (this.listKhoaDaoTao.select2.length > 0) {
              this.editForm.model.kdt_id = this.listKhoaDaoTao.select2[0].id;
          }
          if (this.listQuyetDinh.select2.length > 0) {
              this.editForm.model.qd_id = this.listQuyetDinh.select2[0].id;
          }
          Vue.set(this.editForm, "errors", {});
      },
      create: function () {
          this.resetEditForm();
          editModal.show();
      },
      save: function () {
          if (this.editForm.model.lh_id == null) {
              this.store();
          } else {
              this.update();
          }
      },
      store: function () {
          var vm = this;
          consumer
              .saveOrUpdate(this.editForm.model)
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
                      Vue.set(
                          vm.editForm,
                          "errors",
                          error.response.data.errors
                      );
                  }
              });
      },
      edit: function (lh_id) {
          this.resetEditForm();
          editModal.show();
          consumer.getLopHoc(lh_id).then((data) => {
              this.editForm.model = data;
              this.editForm.reference = { ...this.editForm.model };
          });
      },
      update: function () {
          var vm = this;
          consumer
              .saveOrUpdate(this.editForm.model)
              .then((response) => {
                  if (response.status == 200) {
                      consumer.getListQuyetDinh().then((data) => {
                          vm.listQuyetDinh.select2.push({
                              id: 0,
                              text: "Quyết định mới",
                          });
                          vm.listQuyetDinh.select2.push(
                              ...data.map((item) => {
                                  const [year, month, day] =
                                      item.qd_ngay.split("-");
                                  // Concatenate them in the desired format
                                  const formattedDate = `${day}/${month}/${year}`;
                                  const name =
                                      "QĐ số " +
                                      item.qd_ma +
                                      " ngày " +
                                      formattedDate +
                                      " về việc " +
                                      item.qd_ten;
                                  return {
                                      id: item.qd_id,
                                      text: name,
                                  };
                              })
                          );
                      });
                      vm.resetEditForm();
                      vm.reloadList();
                      editModal.hide();
                      AlertBox.Notify.Success("Cập nhật thành công");
                  }
              })
              .catch((error) => {
                  if (error.response.status == 422) {
                      Vue.set(
                          vm.editForm,
                          "errors",
                          error.response.data.errors
                      );
                  }
              });
      },
      destroy: function (lh_id) {
          var vm = this;
          AlertBox.Comfirm(
              "Xác nhận",
              "Bạn có đồng ý xóa",
              function () {
                  // Ok
                  consumer.destroy(lh_id).then((response) => {
                      vm.reloadList();
                      AlertBox.Notify.Success("Xóa thành công");
                  });
              },
              function () {}
          );
      },
      preActionDuplicate: function (lh_id) {
          consumer.getLopHoc(lh_id).then((data) => {
              this.editForm.model = data;
              this.editForm.reference = { ...this.editForm.model };
              this.editForm.model.lh_ma += " - Copy";
              this.editForm.model.lh_ten += " - Copy";
              duplicateModal.show();
          });
      },
      duplicate: function () {
          var vm = this;
          consumer
              .duplicate(this.editForm.model)
              .then((response) => {
                  if (response.status == 200) {
                      vm.resetEditForm();
                      vm.reloadList();
                      duplicateModal.hide();
                      AlertBox.Notify.Success("Sao chép thành công");
                  }
              })
              .catch((error) => {
                  if (error.response.status == 422) {
                      Vue.set(
                          vm.editForm,
                          "errors",
                          error.response.data.errors
                      );
                  }
              });
      },
  },
};
</script>
