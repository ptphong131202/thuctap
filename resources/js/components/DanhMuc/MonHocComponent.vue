<template>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Môn học
        <button class="btn btn-danger btn-flat btn-xs">
            <a style="color: white;" href="javascript:void(0);"  title="Thêm mới" v-on:click="create()">
              <i class="fa fa-plus"></i> Thêm mới
            </a>
                </button>
          
        <button class="btn btn-success btn-flat btn-xs">
          <a style="color: white;" v-bind:href="excel" ><i class="fa fa-file-excel-o"></i> Thêm môn học
            bằng Excel</a>
                </button>
      </h1>
      <ol class="breadcrumb">
        <li>
          <a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a>
        </li>
        <li class="active">Môn học</li>
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
                      <label>Mã hoặc tên Môn</label>
                      <input type="text" name="search" v-model="filter.search" class="form-control" />
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Hệ đào tạo</label>
                      <select name="hedaotao" class="form-control" v-model="filter.hedaotao"
                        v-on:change="actionChangeHeDaoTaoFilter()">
                        <option v-for="hdt in listHeDaoTao.select2" :key="hdt.id" v-bind:value="hdt.id">
                          {{ hdt.text }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Loại môn</label>
                      <select name="loai" class="form-control" v-model="filter.loai">
                        <option value="-1">Tất cả</option>
                        <option value="1">Bình thường</option>
                        <option value="2">Thi chính trị</option>
                        <option value="3">Thi tốt nghiệp thực hành</option>
                        <option value="4">Thi tốt nghiệp lý thuyết</option>
                        <option value="5">Thi tốt nghiệp khóa luận</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Ngành nghề</label>
                      <select name="nganhnghe" class="form-control" v-model="filter.nganhnghe">
                        <option value="-1">Tất cả</option>
                        <option v-for="nn in listNganhNghe.select2" :key="nn.id" v-bind:value="nn.id">
                          {{ nn.text }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-12" align="center">
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
                    <th class="w-10">Mã</th>
                    <th>Tên môn học</th>
                    <th>Ngành nghề</th>
                    <th class="w-10">Số tín chỉ</th>
                    <th class="w-10">Số tiết/giờ</th>
                    <th class="w-10">Loại môn</th>
                    <th class="w-10">Tích lũy</th>
                    <!-- <th class="w-15">Giảng viên</th> -->
                    <th class="w-90-p text-center">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(mh, index) in table.list.data" :key="mh.mh_id">
                    <td class="text-center">
                      {{
                        index + 1 + (table.list.current_page - 1) * table.list.per_page
                      }}
                    </td>
                    <td>{{ mh.mh_ma }}</td>
                    <td>{{ mh.mh_ten }}</td>
                    <td>{{ mh.nn_ten }}</td>
                    <td>{{ mh.mh_sodonvihoctrinh }}</td>
                    <td>{{ mh.mh_sotiet }}</td>
                    <td>
                      <span v-if="mh.mh_loai == 1">Bình thường</span>
                      <span v-if="mh.mh_loai == 2">Thi chính trị</span>
                      <span v-if="mh.mh_loai == 3">Thi tốt nghiệp thực hành</span>
                      <span v-if="mh.mh_loai == 4">Thi tốt nghiệp lý thuyết</span>
                      <span v-if="mh.mh_loai == 5">Thi tốt nghiệp khóa luận</span>
                    </td>
                    <td>{{ mh.mh_tichluy ? "Có" : "Không" }}</td>
                    <!-- <td>{{ mh.mh_giangvien }}</td> -->
                    <td class="text-center">
                      <button type="button" class="btn bg-orange btn-sm pull-left" title="Thay đổi"
                        v-if="!mh.bang_diem_exists && mh.deleted_at == null" v-on:click="edit(mh.mh_id)">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-danger btn-sm pull-right" title="Xóa"
                        v-if="!mh.bang_diem_exists && mh.deleted_at == null" v-on:click="destroy(mh.mh_id, mh.mh_ten)">
                        <i class="fa fa-trash"></i>
                      </button>
                      <button type="button" class="btn btn-primary btn-sm pull-right" title="Khôi phục"
                        v-if="!mh.bang_diem_exists && mh.deleted_at != null" v-on:click="restore(mh.mh_id, mh.mh_ten)">
                        <i class="fa fa-undo" aria-hidden="true"></i>
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

    <div class="modal fade" id="mon-hoc-edit-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
              {{
                editForm.reference == Object ? "Thêm môn học" : editForm.reference.mh_ten
              }}
            </h4>
          </div>
          <div class="modal-body">
            <form-group :errors="editForm.errors" :field="'hdt_id'">
              <label>Hệ đào tạo</label>
              <select2 v-model="editForm.model.hdt_id" :options="listHeDaoTao.select2Form"></select2>
            </form-group>
            <form-group :errors="editForm.errors" :field="'nn_id'">
              <label>Ngành, nghề</label>
              <select2 v-model="editForm.model.nn_id" :options="listNganhNghe.select2"></select2>
            </form-group>
            <form-group :errors="editForm.errors" :field="'mh_ma'">
              <label>Mã môn học</label>
              <input type="text" class="form-control" v-model="editForm.model.mh_ma" />
            </form-group>
            <form-group :errors="editForm.errors" :field="'mh_ten'">
              <label>Tên môn học</label>
              <input type="text" class="form-control" v-model="editForm.model.mh_ten" />
            </form-group>
            <form-group :errors="editForm.errors" :field="'mh_sodonvihoctrinh'">
              <label>Số tín chỉ</label>
              <input type="text" class="form-control" v-model="editForm.model.mh_sodonvihoctrinh" />
            </form-group>
            <form-group :errors="editForm.errors" :field="'mh_sotiet'">
              <label>Số tiết/giờ</label>
              <input type="text" class="form-control" v-model="editForm.model.mh_sotiet" />
            </form-group>
            <form-group :errors="editForm.errors" :field="'mh_sotiet'">
              <label>Loại môn</label>
              <select class="form-control" v-model="editForm.model.mh_loai">
                <option value="1">Bình thường</option>
                <option value="2">Thi chính trị</option>
                <option value="3">Thi tốt nghiệp thực hành</option>
                <option value="4">Thi tốt nghiệp lý thuyết</option>
                <option value="5">Thi tốt nghiệp khóa luận</option>
              </select>
            </form-group>
            <form-group :errors="editForm.errors" :field="'mh_tichluy'">
              <div class="checkbox">
                <label>
                  <input type="checkbox" v-model="editForm.model.mh_tichluy" /> Tính tích
                  lũy
                </label>
              </div>
            </form-group>
            <!-- <form-group :errors="editForm.errors" :field="'mh_giangvien'">
                              <label>Giảng viên</label>
                              <input type="text" class="form-control" v-model="editForm.model.mh_giangvien" />
                          </form-group> -->
            <!-- <form-group :errors="editForm.errors" :field="'mh_ghichu'">
                              <label>Ghi chú</label>
                              <textarea class="form-control" v-model="editForm.model.mh_ghichu"></textarea>
                          </form-group> -->
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
  </div>
</template>

<script>
const editModal = {
  modal: function () {
    return $("#mon-hoc-edit-modal");
  },
  show: function () {
    this.modal().modal({ keyboard: false, backdrop: "static" });
  },
  hide: function () {
    this.modal().modal("hide");
  },
};

const consumer = {
  getListHeDaoTao: function () {
    const url = "http://localhost/cea-4.0/public/api/he-dao-tao/all";
    return axios.get(url).then((response) => response.data);
  },
  getListNganhNghe: function (hdt_id) {
    const url = "http://localhost/cea-4.0/public/api/nganh-nghe/all?hedaotao=" + hdt_id;
    return axios.get(url).then((response) => response.data);
  },
  getListMonHoc: function () {
    const url = "http://localhost/cea-4.0/public/api/mon-hoc" + window.location.search;
    return axios.get(url).then((response) => response.data);
  },
  getMonHoc: function (mh_id) {
    const url = "http://localhost/cea-4.0/public/api/mon-hoc/" + mh_id;
    return axios.get(url).then((response) => response.data);
  },
  saveOrUpdate: function (formData) {
    if (formData.mh_id == null) {
      return axios.post("http://localhost/cea-4.0/public/api/mon-hoc", formData);
    } else {
      return axios.put("http://localhost/cea-4.0/public/api/mon-hoc/" + formData.mh_id, formData);
    }
  },
  destroy: function (mh_id) {
    return axios.delete("http://localhost/cea-4.0/public/api/mon-hoc/" + mh_id);
  },
  restore: function (mh_id) {
    return axios.put("http://localhost/cea-4.0/public/api/mon-hoc/restore/" + mh_id);
  },
};

const store = {
  table: {
    set list(data) {
      window.localStorage.setItem("mh.table.list", JSON.stringify(data));
    },
    get list() {
      return window.localStorage.getItem("mh.table.list")
        ? JSON.parse(window.localStorage.getItem("mh.table.list"))
        : {};
    },
  },
};

export default {
  props: ["excel", "permissions"],
  mounted() {
    this.quyenTrungCap = this.permissions.includes("trungcap");
    this.quyenCaoDang = this.permissions.includes("caodang");
    if (new URLSearchParams(window.location.search).get("hedaotao") != null) {
      this.filter.hedaotao = new URLSearchParams(window.location.search).get("hedaotao");
    } else {
      this.filter.hedaotao = -1;
    }
    this.reloadList();
    this.loadDanhSachNganhNghe();
  },
  data() {
    return {
      filter: {
        search: new URLSearchParams(window.location.search).get("search"),
        loai:
          new URLSearchParams(window.location.search).get("loai") == null
            ? -1
            : new URLSearchParams(window.location.search).get("loai"),
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
        select2Form: [],
        select2: [],
      },
      listNganhNghe: {
        select2: [],
      },
    };
  },
  methods: {
    actionChangeHeDaoTaoFilter: function () {
      this.listNganhNghe.select2 = this.listNganhNghe.origin.filter((item) => {
        return item.hdt_id == this.filter.hedaotao;
      });
      //   this.filter.nganhnghe = -1;
    },
    loadDanhSachNganhNghe: function () {
      var promHdt = consumer.getListHeDaoTao().then((data) => {
        let dataSelect = data
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

        this.listHeDaoTao.select2Form = dataSelect;
        this.listHeDaoTao.select2 = dataSelect;

        this.listHeDaoTao.select2Form = [
          { id: 0, text: "Dùng chung" },
          ...this.listHeDaoTao.select2Form,
        ];

        this.listHeDaoTao.select2 = [
          { id: -1, text: "Tất cả" },
          { id: 0, text: "Dùng chung" },
          ...this.listHeDaoTao.select2,
        ];
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
    reloadList: function () {
      var vm = this;
      consumer.getListMonHoc().then((data) => {
        Vue.set(vm.table, "list", data);
        store.table.list = data;
      });
    },
    resetEditForm: function () {
      this.editForm = {
        reference: Object,
        model: {
          hdt_id: 0,
          mh_loai: 1,
          mh_tichluy: true,
        },
      };
      Vue.set(this.editForm, "errors", {});
    },
    create: function () {
      this.resetEditForm();
      editModal.show();
    },
    save: function () {
      if (this.editForm.model.mh_id == null) {
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
            Vue.set(vm.editForm, "errors", error.response.data.errors);
          }
        });
    },
    edit: function (mh_id) {
      this.resetEditForm();
      consumer.getMonHoc(mh_id).then((data) => {
        this.editForm.model = data;
        this.editForm.reference = { ...this.editForm.model };
        editModal.show();
      });
    },
    update: function () {
      const newMh_tichluy = typeof this.editForm.model.mh_tichluy === 'boolean' ? this.editForm.model.mh_tichluy : false;
      const newModel = {...this.editForm.model, mh_tichluy: newMh_tichluy};
      var vm = this;
      consumer
        .saveOrUpdate(newModel)
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
    destroy: function (mh_id, mh_ten) {
      var vm = this;
      AlertBox.Comfirm(
        "Xác nhận",
        `Bạn có đồng ý xóa môn học: "${mh_ten}" không?`,

        function () {
          // Ok
          consumer
            .destroy(mh_id)
            .then((response) => {
              vm.reloadList();
              AlertBox.Notify.Success("Xóa môn học thành công");
            })
            .catch((error) => {
              vm.reloadList();
              AlertBox.Notify.Failure("Không thể xóa môn học này!");
            });
        },
        function () { }
      );
    },
    restore: function (mh_id, mh_ten) {
      var vm = this;
      AlertBox.Comfirm(
        "Xác nhận",
        `Bạn có đồng ý khôi phục môn học: "${mh_ten}" không?`,
        function () {
          // Ok
          consumer
            .restore(mh_id)
            .then((response) => {
              vm.reloadList();
              AlertBox.Notify.Success("Khôi phục môn học thành công");
            })
            .catch((error) => {
              vm.reloadList();
              AlertBox.Notify.Failure("Khôi phục môn học thất bại");
            });
        },
        function () { }
      );
    },
  },
};
</script>
