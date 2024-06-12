<template>
  <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
          <h1>
          Ngành, nghề
                <button class="btn btn-danger">
                        <a href="javascript:void(0);"  style="color: white;"
                                title="Thêm mới"
                                v-on:click="create()">
                            <i class="fa fa-plus"></i> Thêm mới
                        </a>
                </button>
          </h1>
          <ol class="breadcrumb">
              <li>
                  <a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a>
              </li>
              <li class="active">Ngành, nghề</li>
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
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label
                                              >Mã hoặc tên ngành, nghề</label
                                          >
                                          <input
                                              type="text"
                                              name="search"
                                              v-model="filter.search"
                                              class="form-control"
                                          />
                                      </div>
                                  </div>
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Hệ đào tạo</label>
                                          <select
                                              name="hedaotao"
                                              class="form-control"
                                              v-model="filter.hedaotao"
                                          >
                                              <option
                                                  v-for="hdt in listHeDaoTao.select2"
                                                  :key="hdt.id"
                                                  v-bind:value="hdt.id"
                                              >
                                                  {{ hdt.text }}
                                              </option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-md-12" align="center">
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
                      <div class="box-body">
                          <table
                              class="table table-striped table-bordered no-margin"
                          >
                              <thead>
                                  <tr>
                                      <th class="w-3 text-center">#</th>
                                      <th>Mã</th>
                                      <th>Ngành, nghề</th>
                                      <th>Hệ đào tạo</th>
                                      <th class="w-90-p text-center">
                                          Hành động
                                      </th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr
                                      v-for="(nn, index) in table.list.data"
                                      :key="nn.nn_id"
                                  >
                                      <td class="text-center">
                                          {{
                                              index +
                                              1 +
                                              (table.list.current_page - 1) *
                                                  table.list.per_page
                                          }}
                                      </td>
                                      <td>{{ nn.nn_ma }}</td>
                                      <td>{{ nn.nn_ten }}</td>
                                      <td>{{ nn.he_dao_tao.hdt_ten }}</td>

                                      <td class="text-center">
                                          <a
                                              :href="nn.monhoc_url"
                                              class="btn bg-purple btn-sm"
                                              title="Chi tiết môn học"
                                          >
                                              <i class="fa fa-eye"></i>
                                          </a>
                                          <button
                                              type="button"
                                              class="btn bg-orange btn-sm pull-left"
                                              title="Thay đổi"
                                              v-on:click="edit(nn.nn_id)"
                                          >
                                              <i class="fa fa-edit"></i>
                                          </button>
                                          <button
                                              type="button"
                                              class="btn btn-danger btn-sm pull-right"
                                              title="Xóa"
                                              v-if="
                                                  !nn.khoa_dao_tao_exists &&
                                                  !nn.mon_hoc_exists
                                              "
                                              v-on:click="destroy(nn.nn_id)"
                                          >
                                              <i class="fa fa-trash"></i>
                                          </button>
                                          <button
                                              type="button"
                                              class="btn btn-info btn-sm pull-right"
                                              title="Sao chép ngành"
                                              style="  color: '';           cursor: pointer;       "
                                              v-on:click="duplicate(nn.nn_id)"

                                          >
                                              <i class="fa fa-copy"></i>
                                          </button>
                                          <!-- v-on:click="preActionDuplicate( kdt.kdt_id   )   " -->
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

      <div class="modal fade" id="nganh-nghe-edit-modal">
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
                                  ? "Thêm ngành, nghề"
                                  : editForm.reference.nn_ten
                          }}
                      </h4>
                  </div>
                  <div class="modal-body">
                      <form-group :errors="editForm.errors" :field="'hdt_id'">
                          <label>Hệ đào tạo</label>
                          <select2
                              v-model="editForm.model.hdt_id"
                              :options="listHeDaoTao.select2"
                          ></select2>
                      </form-group>
                      <form-group :errors="editForm.errors" :field="'nn_ma'">
                          <label>Mã</label>
                          <input
                              type="text"
                              class="form-control"
                              v-model="editForm.model.nn_ma"
                          />
                      </form-group>
                      <form-group :errors="editForm.errors" :field="'nn_ten'">
                          <label>Ngành, nghề</label>
                          <input
                              type="text"
                              class="form-control"
                              v-model="editForm.model.nn_ten"
                          />
                      </form-group>
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
      <div class="modal fade" id="duplicate-nganh-nghe-modal">
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
                                  ? "Thêm ngành, nghề"
                                  : editForm.reference.nn_ten
                          }}
                      </h4>
                  </div>
                  <div class="modal-body">
                      <form-group :errors="editForm.errors" :field="'hdt_id'">
                          <label>Hệ đào tạo</label>
                          <select2
                              v-model="editForm.model.hdt_id"
                              :options="listHeDaoTao.select2"
                          ></select2>
                      </form-group>
                      <form-group :errors="editForm.errors" :field="'nn_ma'">
                          <label>Mã</label>
                          <input
                              type="text"
                              class="form-control"
                              v-model="editForm.model.nn_ma"
                          />
                      </form-group>
                      <form-group :errors="editForm.errors" :field="'nn_ten'">
                          <label>Ngành, nghề</label>
                          <input
                              type="text"
                              class="form-control"
                              v-model="editForm.model.nn_ten"
                          />
                      </form-group>
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
                          v-on:click="Copysave"
                      >
                          <i class="fa fa-save"></i> Sao chép
                      </button>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
const editModal = {
  modal: function () {
      return $("#nganh-nghe-edit-modal");
  },
  show: function () {
      this.modal().modal({ keyboard: false, backdrop: "static" });
  },
  hide: function () {
      this.modal().modal("hide");
  },
};
const duplicateModal = {
  modal: function () {
      return $("#duplicate-nganh-nghe-modal");
  },
  show: function () {
      this.modal().modal({ keyboard: false, backdrop: "static" });
  },
  hide: function () {
      this.modal().modal("hide");
  },
};

const consumer = {
  getListNganhNghe: function () {
      const url =
          "http://localhost/cea-4.0/public/api/nganh-nghe" +
          window.location.search;
      return axios.get(url).then((response) => response.data);
  },
  getNganhNghe: function (nn_id) {
      const url = "http://localhost/cea-4.0/public/api/nganh-nghe/" + nn_id;
      return axios.get(url).then((response) => response.data);
  },
  getListHeDaoTao: function () {
      const url = "http://localhost/cea-4.0/public/api/he-dao-tao/all";
      return axios.get(url).then((response) => response.data);
  },
  saveOrUpdate: function (formData) {
      if (formData.nn_id == null) {
          return axios.post("http://localhost/cea-4.0/public/api/nganh-nghe", formData);
      } else {
          return axios.put("http://localhost/cea-4.0/public/api/nganh-nghe/" + formData.nn_id, formData);
      }
  },
  destroy: function (nn_id) {
      return axios.delete("http://localhost/cea-4.0/public/api/nganh-nghe/" + nn_id);
  },
  duplicate: function (formData) {
      return axios.post(
          "http://localhost/cea-4.0/public/api/nganh-nghe/duplicate/" + formData.nn_id,
          formData
      );
  },
};

const store = {
  table: {
      set list(data) {
          window.localStorage.setItem("nn.table.list", JSON.stringify(data));
      },
      get list() {
          return window.localStorage.getItem("nn.table.list")
              ? JSON.parse(window.localStorage.getItem("nn.table.list"))
              : {};
      },
  },
};

export default {
  props: ["permissions"],
  mounted() {
      //  this.quyenTrungCap = this.permissions.includes("trungcap");
      //   this.quyenCaoDang = this.permissions.includes("caodang");

      if (
          new URLSearchParams(window.location.search).get("hedaotao") != null
      ) {
          this.filter.hedaotao = new URLSearchParams(
              window.location.search
          ).get("hedaotao");
      } else if (
          new URLSearchParams(window.location.search).get("hedaotao") ==
              null &&
          this.quyenCaoDang &&
          this.quyenTrungCap
      ) {
          this.filter.hedaotao = -1;
      } else if (this.quyenCaoDang) {
          this.filter.hedaotao = 4;
      } else {
          this.filter.hedaotao = 5;
      }
      this.reloadList();
      this.loadDanhSachHeDaoTao();
  },
  data() {
      return {
          quyenTrungCap: true,
          quyenCaoDang: true,
          filter: {
              search: new URLSearchParams(window.location.search).get(
                  "search"
              ),
              hedaotao:
                  new URLSearchParams(window.location.search).get(
                      "hedaotao"
                  ) == null
                      ? -1
                      : new URLSearchParams(window.location.search).get(
                            "hedaotao"
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
          listHeDaoTao: {
              select2: [],
          },
      };
  },
  methods: {
      loadDanhSachHeDaoTao: function () {
          consumer.getListHeDaoTao().then((data) => {
              this.listHeDaoTao.select2 = data
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
                  this.listHeDaoTao.select2 = [
                      { id: -1, text: "Tất cả" },
                      ...this.listHeDaoTao.select2,
                  ];
              }
          });
      },
      reloadList: function () {
          var vm = this;
          consumer.getListNganhNghe().then((data) => {
              Vue.set(vm.table, "list", data);
              store.table.list = data;
          });
      },
      resetEditForm: function () {
          this.editForm = {
              reference: Object,
              model: {},
          };
          Vue.set(this.editForm, "errors", {});
      },
      create: function () {
          this.resetEditForm();
          editModal.show();
      },
      save: function () {
          if (this.editForm.model.nn_id == null) {
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
      edit: function (nn_id) {
          this.resetEditForm();
          consumer.getNganhNghe(nn_id).then((data) => {
              this.editForm.model = data;
              this.editForm.reference = { ...this.editForm.model };
              editModal.show();
          });
      },
      duplicate: function (nn_id) {
          this.resetEditForm();
          consumer.getNganhNghe(nn_id).then((data) => {
              this.editForm.model = data;
              this.editForm.reference = { ...this.editForm.model };
              this.editForm.model.nn_ma += " - Copy";
              this.editForm.model.nn_ten += " - Copy";
              duplicateModal.show();
          });
      },
      update: function () {
          var vm = this;
          consumer
              .saveOrUpdate(this.editForm.model)
              .then((response) => {
                  if (response.status == 200) {
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
      destroy: function (nn_id) {
          var vm = this;
          AlertBox.Comfirm(
              "Xác nhận",
              "Bạn có đồng ý xóa",
              function () {
                  // Ok
                  consumer
                      .destroy(nn_id)
                      .then((response) => {
                          vm.reloadList();
                          AlertBox.Notify.Success("Xóa thành công");
                      })
                      .catch((error) => {
                          vm.reloadList();
                          AlertBox.Notify.Failure("Không thể xóa");
                      });
              },
              function () {
                  // No
              }
          );
      },
      Copysave: function(){
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
      }
  },
};
</script>
