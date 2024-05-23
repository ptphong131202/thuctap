<template>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Chương trình đào tạo
        <small>
          <a
            href="javascript:void(0);"
            class="btn btn-danger btn-flat btn-xs"
            title="Thêm mới"
            v-on:click="create()"
          >
            <i class="fa fa-plus"></i> Thêm mới
          </a>
        </small>
      </h1>
      <ol class="breadcrumb">
        <li>
          <a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a>
        </li>
        <li class="active">Chương trình đào tạo</li>
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
                      <label>Mã hoặc tên Chương trình</label>
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
                      <label>Hệ đào tạo</label>
                      <select
                        name="hedaotao"
                        class="form-control"
                        v-model="filter.hedaotao"
                        @change="actionChangeHeDaoTaoFilter"
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
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Ngành nghề</label>
                      <select
                        name="nganhnghe"
                        class="form-control"
                        v-model="filter.nganhnghe"
                      >
                        <option value="-1">Tất cả</option>
                        <option
                          v-for="nn in listNganhNghe.select2"
                          :key="nn.id"
                          v-bind:value="nn.id"
                        >
                          {{ nn.text }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2" align="center">
                    <button type="submit" class="btn btn-default" style="margin-top: 2rem;">
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
            <div class="box-body table-responsive">
              <table class="table table-striped table-bordered no-margin">
                <thead>
                  <tr>
                    <th class="w-3 text-center">#</th>
                    <th class="w-10">Mã</th>
                    <th>Tên chương trình đào tạo</th>
                    <th class="w-8">Hệ đào tạo</th>
                    <th>Ngành, nghề</th>
                    <th class="w-4">Khóa</th>
                    <th class="w-8">Hệ</th>
                    <!-- <th class="w-10">Số học kỳ</th> -->
                    <th class="w-100-p text-center">Môn học</th>
                    <th class="w-90-p text-center">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(kdt, index) in table.list.data" :key="kdt.kdt_id">
                    <td class="text-center">
                      {{
                        index + 1 + (table.list.current_page - 1) * table.list.per_page
                      }}
                    </td>
                    <td>{{ kdt.kdt_ma }}</td>
                    <td>
                      {{ kdt.kdt_ten }}
                    </td>
                    <td>
                      {{ kdt.he_dao_tao.hdt_ten }}
                    </td>
                    <td>
                      {{ kdt.nganh_nghe != null ? kdt.nganh_nghe.nn_ten : "" }}
                    </td>
                    <td>
                      {{ kdt.kdt_khoa }}
                    </td>
                    <td>
                      {{ kdt.kdt_he }}
                    </td>
                    <!-- <td>{{ kdt.kdt_hocky }}</td> -->
                    <td>
                      <a
                        :href="kdt.edit_url"
                        class="btn bg-purple btn-flat btn-sm"
                        title="Danh sách môn học"
                      >
                        <i class="fa fa-cogs"></i> Danh sách
                      </a>
                    </td>
                    <td class="text-center">
                      <button
                        type="button"
                        class="btn bg-orange btn-sm pull-left"
                        title="Thay đổi"
                        v-on:click="edit(kdt.kdt_id)"
                      >
                        <i class="fa fa-edit"></i>
                      </button>
                      <button
                        type="button"
                        class="btn btn-danger btn-sm pull-right"
                        title="Xóa"
                        v-if="!kdt.lop_hoc_exists"
                        v-on:click="destroy(kdt.kdt_id)"
                      >
                        <i class="fa fa-trash"></i>
                      </button>

                      <button
                        type="button"
                        class="btn btn-info btn-sm pull-right"
                        title="Sao chép CTĐT"
                        style=" cursor: pointer"
                        v-on:click="preActionDuplicate(kdt.kdt_id)"
                      >
                      <i class="fa fa-copy"></i>
                      </button>
                      <!-- <a
                        type="button"
                        class="btn-sm pull-right"
                        title="Sao chép"
                        style="color: black; cursor: pointer"
                        v-on:click="preActionDuplicate(kdt.kdt_id)"
                        ><i class="fa fa-copy"></i>
                      </a> -->
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

    <div class="modal fade" id="khoa-dao-tao-edit-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
              {{
                editForm.reference == Object
                  ? "Thêm chương trình đào tạo"
                  : editForm.reference.kdt_ten
              }}
            </h4>
          </div>
          <div class="modal-body">
            <form-group :errors="editForm.errors" :field="'nn_id'">
              <label>Ngành, nghề</label>
              <select2
                v-model="editForm.model.nn_id"
                :options="listNganhNghe.select2"
              ></select2>
            </form-group>
            <form-group :errors="editForm.errors" :field="'kdt_ma'">
              <label>Mã chương trình đào tạo</label>
              <input type="text" class="form-control" v-model="editForm.model.kdt_ma" />
            </form-group>
            <form-group :errors="editForm.errors" :field="'kdt_ten'">
              <label>Tên chương trình đào tạo</label>
              <input type="text" class="form-control" v-model="editForm.model.kdt_ten" />
            </form-group>
            <form-group :errors="editForm.errors" :field="'kdt_khoa'">
              <label>Khóa</label>
              <input
                type="number"
                class="form-control"
                v-model="editForm.model.kdt_khoa"
              />
            </form-group>
            <form-group :errors="editForm.errors" :field="'kdt_he'">
              <label>Hệ</label>
              <input type="text" class="form-control" v-model="editForm.model.kdt_he" />
            </form-group>
            <form-group :errors="editForm.errors" :field="'kdt_ghichu'">
              <label>Ghi chú</label>
              <textarea v-model="editForm.model.kdt_ghichu" class="form-control" />
            </form-group>
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

    <div class="modal fade" id="duplicate-khoa-dao-tao-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Sao chép chương trình đào tạo</h4>
          </div>
          <div class="modal-body">
            <form-group :errors="editForm.errors" :field="'nn_id'">
              <label>Sao chép</label>
              <div>
                <span
                  >{{ editForm.reference.kdt_ma }} -
                  {{ editForm.reference.kdt_ten }}</span
                >
              </div>
            </form-group>
            <form-group :errors="editForm.errors" :field="'kdt_ma'">
              <label>Mã chương trình đào tạo mới</label>
              <input type="text" class="form-control" v-model="editForm.model.kdt_ma" />
            </form-group>
            <form-group :errors="editForm.errors" :field="'kdt_ten'">
              <label>Tên chương trình đào tạo mới</label>
              <input type="text" class="form-control" v-model="editForm.model.kdt_ten" />
            </form-group>
            <div class="callout callout-info">
              <h4>Lưu ý!</h4>

              <p>
                Chỉ có thể sao chép chương trình đào tạo cùng hệ đào tạo và cùng ngành
                nghề đào tạo
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
              <i class="fa fa-times-circle-o"></i> Hủy bỏ
            </button>
            <button type="button" class="btn bg-purple" v-on:click="duplicate">
              <i class="fa fa-save"></i> Lưu
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
    return $("#khoa-dao-tao-edit-modal");
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
    return $("#duplicate-khoa-dao-tao-modal");
  },
  show: function () {
    this.modal().modal({ keyboard: false, backdrop: "static" });
  },
  hide: function () {
    this.modal().modal("hide");
  },
};

const consumer = {
  getListNganhNghe: function (hdt_id) {
    const url = "http://localhost/tthl/public/api/nganh-nghe/all?hedaotao=" + hdt_id; /** Phong api */
    return axios.get(url).then((response) => response.data);
  },
  getListHeDaoTao: function () {
    const url = "http://localhost/tthl/public/api/he-dao-tao/all";/** Phong api */
    return axios.get(url).then((response) => response.data);
  },
  getListKhoaDaoTao: function () {
    const url = "http://localhost/tthl/public/api/khoa-dao-tao" + window.location.search;/** Phong api */
    return axios.get(url).then((response) => response.data);
  },
  getKhoaDaotao: function (kdt_id) {
    const url = "http://localhost/tthl/public/api/khoa-dao-tao/" + kdt_id;/** Phong api */
    return axios.get(url).then((response) => response.data);
  },
  saveOrUpdate: function (formData) {
    if (formData.kdt_id == null) {
      return axios.post("http://localhost/tthl/public/api/khoa-dao-tao", formData);/** Phong api */
    } else {
      return axios.put("http://localhost/tthl/public/api/khoa-dao-tao/" + formData.kdt_id, formData);/** Phong api */
    }
  },
  destroy: function (kdt_id) {
    return axios.delete("http://localhost/tthl/public/api/khoa-dao-tao/" + kdt_id);/** Phong api */
  },
  duplicate: function (formData) {
    return axios.post("http://localhost/tthl/public/api/khoa-dao-tao/duplicate/" + formData.kdt_id, formData);/** Phong api */
  },
};

const store = {
  table: {
    set list(data) {
      window.localStorage.setItem("kdt.table.list", JSON.stringify(data));
    },
    get list() {
      return window.localStorage.getItem("kdt.table.list")
        ? JSON.parse(window.localStorage.getItem("kdt.table.list"))
        : {};
    },
  },
};

export default {
  props: ["permissions"],
  mounted() {
    /* this.quyenTrungCap = this.permissions.includes("trungcap");
    this.quyenCaoDang = this.permissions.includes("caodang"); */
    if (new URLSearchParams(window.location.search).get("hedaotao") != null) {
      this.filter.hedaotao = new URLSearchParams(window.location.search).get("hedaotao");
    } else if (
      new URLSearchParams(window.location.search).get("hedaotao") == null &&
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
  },
  data() {
    return {
      quyenCaoDang: true,
      quyenTrungCap: true,
      filter: {
        search: new URLSearchParams(window.location.search).get("search"),
        hedaotao:
          new URLSearchParams(window.location.search).get("hedaotao") == null
            ? -1
            : new URLSearchParams(window.location.search).get("hedaotao"),
        nganhnghe:
          new URLSearchParams(window.location.search).get("nganhnghe") == null
            ? -1
            : new URLSearchParams(window.location.search).get("nganhnghe"),
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
      listNganhNghe: {
        origin: [],
        select2: [],
      },
    };
  },
  methods: {
    loadDanhSachNganhNghe: function () {
      var promHdt = consumer.getListHeDaoTao().then((data) => {
        this.listHeDaoTao.select2 = data
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

        if (this.quyenCaoDang && this.quyenTrungCap) {
          this.listHeDaoTao.select2.unshift({
            id: -1,
            text: "Tất cả",
            selected: true,
          });
        }
      });

      var promNn = consumer.getListNganhNghe().then((data) => {
        this.listNganhNghe.origin = data
          .filter((item) => {
            return (
              (item.he_dao_tao.hdt_ten.toLowerCase() == "cao đẳng" &&
                this.quyenCaoDang) ||
              (item.he_dao_tao.hdt_ten.toLowerCase() == "trung cấp" && this.quyenTrungCap)
            );
          })
          .map((item) => {
            return {
              id: item.nn_id,
              hdt_id: item.hdt_id,
              text: item.nn_ten + " - " + item.he_dao_tao.hdt_ten,
            };
          });
      });

      Promise.all([promHdt, promNn]).then((res) => {
        this.actionChangeHeDaoTaoFilter();
      });
    },
    actionChangeHeDaoTaoFilter: function () {
      this.listNganhNghe.select2 = this.listNganhNghe.origin.filter((item) => {
        return item.hdt_id == this.filter.hedaotao;
      });
      this.filter.nganhnghe = -1;
    },
    reloadList: function () {
      var vm = this;
      consumer.getListKhoaDaoTao().then((data) => {
        Vue.set(vm.table, "list", data);
        store.table.list = data;
      });

      if (this.listNganhNghe.origin.length == 0) {
        this.loadDanhSachNganhNghe();
      }
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
      if (this.editForm.model.kdt_id == null) {
        this.store();
      } else {
        this.update();
      }
    },
    store: function () {
      var vm = this;
      if (this.editForm.model.nn_id != null) {
        this.listNganhNghe.select2.forEach((element) => {
          if (element.id == this.editForm.model.nn_id) {
            this.editForm.model.hdt_id = element.hdt_id;
          }
        });
      }
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
            Vue.set(vm.editForm, "errors", error.response.data.errors);
          }
        });
    },
    edit: function (kdt_id) {
      this.resetEditForm();
      consumer.getKhoaDaotao(kdt_id).then((data) => {
        this.editForm.model = data;
        this.editForm.reference = { ...this.editForm.model };
        editModal.show();
      });
    },
    update: function () {
      var vm = this;
      if (this.editForm.model.nn_id != null) {
        this.listNganhNghe.select2.forEach((element) => {
          if (element.id == this.editForm.model.nn_id) {
            this.editForm.model.hdt_id = element.hdt_id;
          }
        });
      }
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
            Vue.set(vm.editForm, "errors", error.response.data.errors);
          }
        });
    },
    destroy: function (kdt_id) {
      var vm = this;
      AlertBox.Comfirm(
        "Xác nhận",
        "Bạn có đồng ý xóa",
        function () {
          // Ok
          consumer
            .destroy(kdt_id)
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
    preActionDuplicate: function (kdt_id) {
      consumer.getKhoaDaotao(kdt_id).then((data) => {
        this.editForm.model = data;
        this.editForm.reference = { ...this.editForm.model };
        this.editForm.model.kdt_ma += " - Copy";
        this.editForm.model.kdt_ten += " - Copy";
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
            Vue.set(vm.editForm, "errors", error.response.data.errors);
          }
        });
    },
  },
};
</script>
