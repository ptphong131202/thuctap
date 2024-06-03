<template>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Nhập điểm bằng file Excel</h1>
      <ol class="breadcrumb">
        <li>
          <a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a>
        </li>
        <li class="active">Nhập điểm bằng file Excel</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="box box-widget" id="panel-sinhvien">
            <div class="box-header">
              <h3 class="box-title">
                <button class="btn btn-default" onclick="window.history.back()">
                  <i class="fa fa-share"></i> Trở về danh sách
                </button>
                <input type="file" class="hidden" id="import-file"
                  accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                  v-on:change="actionSelectFile" />
                <button type="button" class="btn btn-default" v-on:click="actionShowSelectFile">
                  <i class="fa fa-file-excel-o"></i> Chọn tập tin excel
                </button>
                <!-- <a v-if="lh_nienche == 0" href="/sample/import-diem-dotthi-2020-sample.xlsx" class="btn btn-default" download>
                                    <i class="fa fa-download"></i> Tải mẫu
                                </a>
                                <a v-if="lh_nienche == 1" href="/sample/import-diem-dotthi-2022-sample.xlsx" class="btn btn-default" download>
                                    <i class="fa fa-download"></i> Tải mẫu
                                </a> -->

                <div class="btn-group">
                  <button type="button" class="btn btn-default">
                    <i class="fa fa-download"></i> Tải mẫu
                  </button>
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                    aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="/sample/import-diem-dotthi-2020-sample.xlsx">QC-2020</a>
                    </li>
                    <li>
                      <a href="/sample/import-diem-dotthi-2022-sample.xlsx">QC-2022</a>
                    </li>
                  </ul>
                </div>
              </h3>
              <button type="button" class="btn bg-purple" v-on:click="actionSave">
                <i class="fa fa-save"></i> Lưu
              </button>
              <!-- <div class="box-tool pull-right">
                                <div v-if="table.list.length > 0" style="max-width:300px;display:inline-block;">
                                    <button class="btn btn-danger btn-flat" v-on:click="save();">Lưu</button>
                                </div>
                            </div> -->
            </div>

            <div id="panel-diem">
              <table class="table table-hover table-striped table-bordered no-margin">
                <thead>
                  <tr>
                    <th class="w-3 text-center">#</th>
                    <th class="text-center w-5">
                      MSHS/MSSV
                      <input v-model="filter.sv_ma" v-on:keyup="changeFilter" type="text" class="form-control input-sm"
                        placeholder="Tìm kiếm MSSV" />
                    </th>
                    <th colspan="2" class="text-center">
                      Họ và tên
                      <input v-model="filter.sv_hoten" v-on:keyup="changeFilter" type="text"
                        class="form-control input-sm" placeholder="Tìm kiếm theo tên..." />
                    </th>
                    <th class="text-center">Môn Chính trị</th>
                    <th class="text-center">Môn Lý thuyết</th>
                    <th class="text-center">Môn Thực hành</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(sv, indexSv) in editForm.model.data" :key="indexSv">
                    <td class="text-center">{{ indexSv + 1 }}</td>
                    <td class="text-center">{{ sv.sv_ma }}</td>
                    <td class="w-10">{{ sv.sv_ho }}</td>
                    <td class="w-10">{{ sv.sv_ten }}</td>

                    <td>
                      <div style="min-width: 100px" v-if="lh_nienche == 0">
                        <form-group :errors="editForm.errors" :field="'data.' + indexSv + '.diemchinhtri'">
                          <input type="number" v-model="sv.chinhtri" class="form-control" />
                        </form-group>
                      </div>
                    </td>
                    <td>
                      <div style="min-width: 100px">
                        <form-group :errors="editForm.errors" :field="'data.' + indexSv + '.diemlythuyet'">
                          <input type="number" v-model="sv.lythuyet" class="form-control" />
                        </form-group>
                      </div>
                    </td>
                    <td>
                      <div style="min-width: 100px">
                        <form-group :errors="editForm.errors" :field="'data.' + indexSv + '.diemthuchanh'">
                          <input type="number" v-model="sv.thuchanh" class="form-control" />
                        </form-group>
                      </div>
                    </td>
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
            <div class="box-footer">
              <button class="btn btn-default" onclick="window.history.back()">
                <i class="fa fa-share"></i> Trở về danh sách
              </button>
              <button type="button" class="btn bg-purple" v-on:click="actionSave">
                <i class="fa fa-save"></i> Lưu
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->

    <!-- import excel -->
    <div class="modal fade" id="modal-importExcel">
      <div class="modal-dialog" style="width: 90%">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
            <h4 class="modal-title">
              Import điểm bằng excel - Xem lại dữ liệu trước khi lưu
            </h4>
          </div>
          <div class="modal-body" style="height: 80vh; overflow-y: auto">
            <div class="box-body table-responsive">
              <table class="table table-striped table-bordered no-margin">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Mã sinh viên</th>
                    <th class="text-center">Thông tin sinh viên</th>
                    <th class="text-center">Môn Khóa luận</th>
                    <th class="text-center" v-if="lh_nienche == 0">Môn Chính trị</th>
                    <th class="text-center">Môn Lý thuyết</th>
                    <th class="text-center">Môn Thực hành</th>
                    <th class="text-center">Ghi chú</th>
                    <th class="text-left">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(sv, index) in table.list" :key="sv.sv_id">
                    <td class="text-center">{{ index + 1 }}</td>
                    <td class="text-center">
                      {{ sv.sv_ma }}
                      <errors-validate :errors="table.errors" :field="'data.' + index + '.sv_ma'" />
                    </td>
                    <td>
                      <b>{{ sv.sv_ho }} {{ sv.sv_ten }}</b>
                    </td>
                    <td>
                      {{ sv.khoaluan }}
                      <errors-validate :errors="table.errors" :field="'data.' + index + '.khoaluan'" />
                    </td>
                    <td v-if="lh_nienche == 0">
                      {{ sv.chinhtri }}
                      <errors-validate :errors="table.errors" :field="'data.' + index + '.chinhtri'" />
                    </td>
                    <td>
                      {{ sv.lythuyet }}
                      <errors-validate :errors="table.errors" :field="'data.' + index + '.lythuyet'" />
                    </td>
                    <td>
                      {{ sv.thuchanh }}
                      <errors-validate :errors="table.errors" :field="'data.' + index + '.thuchanh'" />
                    </td>
                    <td>
                      {{ sv.ghichu }}
                    </td>
                    <td>
                      <button class="btn btn-warning btn-flat btn-sm" v-on:click="preUpdate(index)">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button class="btn btn-danger btn-flat btn-sm" v-on:click="deleteItem(index)">
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="table.list.length == 0">
                    <td colspan="99" class="text-center">Không tìm thấy dữ liệu</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-danger btn-flat" v-on:click="save()">Lưu</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="sinh-vien-edit-modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
              {{ editForm.reference.sv_ho + " " + editForm.reference.sv_ten }}
            </h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <form-group :errors="editForm.errors" :field="'khoaluan'">
                  <label>Điểm khóa luận</label>
                  <input type="text" class="form-control" v-model="editForm.model.khoaluan" />
                </form-group>
                <form-group v-if="lh_nienche == 0" :errors="editForm.errors" :field="'chinhtri'">
                  <label>Điểm chính trị</label>
                  <input type="text" class="form-control" v-model="editForm.model.chinhtri" />
                </form-group>
                <form-group :errors="editForm.errors" :field="'lythuyet'">
                  <label>Điểm lý thuyết</label>
                  <input type="text" class="form-control" v-model="editForm.model.lythuyet" />
                </form-group>
                <form-group :errors="editForm.errors" :field="'thuchanh'">
                  <label>Điểm thực hành</label>
                  <input type="text" class="form-control" v-model="editForm.model.thuchanh" />
                </form-group>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
              <i class="fa fa-times-circle-o"></i> Hủy bỏ
            </button>
            <button type="button" class="btn bg-purple" v-on:click="update()">
              <i class="fa fa-save"></i> Lưu
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="chon-sheet-modal">
      <div class="modal-dialog">
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
            <table class="table mb-0" style="margin-bottom: 0px">
              <tr v-for="(sheet, index) in listSheet.list" :key="sheet.id">
                <td>
                  <button class="btn btn-primary text-left btn-block" v-on:click="selectSheet(index)"
                    style="text-align: left; margin-bottom: 6px">
                    {{ index + 1 }}. {{ sheet.title }}
                  </button>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const chonSheetModal = {
  modal: function () {
    return $("#chon-sheet-modal");
  },
  show: function () {
    this.modal().modal({ keyboard: false, backdrop: "static" });
  },
  hide: function () {
    this.modal().modal("hide");
  },
};

const editModal = {
  modal: function () {
    return $("#sinh-vien-edit-modal");
  },
  show: function () {
    this.modal().modal({ keyboard: false, backdrop: "static" });
  },
  hide: function () {
    this.modal().modal("hide");
  },
};

const consumer = {
  getLopHoc: function (lh_id) {
    const url = "http://localhost/cea_3.0/public/api/lop-hoc/" + lh_id;
    return axios.get(url).then((response) => response.data);
  },
  getMonHoc: function (mh_id) {
    const url = "http://localhost/cea_3.0/public/api/mon-hoc/" + mh_id;
    return axios.get(url).then((response) => response.data);
  },
  getBangDiem: function (lh_id, mh_id, dt_id) {
    // bd_type = 1 là bảng điểm môn học
    const url = `http://localhost/cea_3.0/public/api/nhap-diem/${lh_id}/bang-diem-dot-thi?mh_id=${mh_id}&dt_id=${dt_id}`;
    return axios.get(url).then((response) => response.data);
  },
  saveBangDiem: function (formData) {
    const url = `http://localhost/cea_3.0/public/api/nhap-diem/${formData.lh_id}/bang-diem-dot-thi-loai0`;
    return axios.post(url, formData);
  },
  save: function (formData, dt_id) {
    return axios.post("http://localhost/cea_3.0/public/api/dot-thi/" + dt_id + "/nhap-diem-excel", formData);
  },
  uploadFile: function (file, lh_nienche) {
    var formData = new FormData();
    var headers = {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    };
    formData.append("excel_file", file);
    formData.append("lh_nienche", lh_nienche);
    return axios.post("http://localhost/cea_3.0/public/api/excel/import-diemdotthi", formData, headers);
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

const panelSinhVien = {
  selector: function () {
    return "#panel-sinhvien";
  },
  block: function () {
    AlertBox.Loading.Block(this.selector(), "Đang tải");
  },
  unblock: function () {
    AlertBox.Loading.Unblock(this.selector());
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

export default {
  props: ["dt_id", "router", "lh_id", "lh_nienche"],
  mounted() {
    this.loadLopHoc();
  },
  data() {
    return {
      filter: {
        sv_ma: "",
        sv_hoten: "",
      },
      table: {
        list: [],
        errors: Object,
        password: "",
      },
      listSheet: {
        list: [],
      },
      editForm: {
        reference: Object,
        model: {},
        errors: Object,
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
    preImportDiem: function () {
      $("#modal-importExcel").modal("show");
    },
    selectSheet: function (number) {
      this.table.list = this.listSheet.list[number].rows;
      console.log(this.listSheet.list[number].rows);
      chonSheetModal.hide();
    },
    resetEditForm: function () {
      this.editForm = {
        reference: Object,
        model: {
          sv_gioitinh: 1,
          qd_id: -1,
          lh_id: this.lh_id,
        },
      };
      Vue.set(this.editForm, "errors", {});
    },
    preUpdate: function (index) {
      this.resetEditForm();
      this.editForm.model = { ...this.table.list[index] };
      this.editForm.model.index = index;
      this.editForm.reference = { ...this.editForm.model };
      editModal.show();
    },
    update: function () {
      this.table.list[this.editForm.model.index] = this.editForm.model;
      this.resetEditForm();
      editModal.hide();
    },
    save: function () {
      var vm = this;
      if (this.dt_id != null) {
        panelSinhVien.block();
        consumer
          .save({ data: vm.table.list, lh_id: this.lh_id }, this.dt_id)
          .then((response) => {
            if (response.status == 200) {
              AlertBox.Notify.Success("Thêm thành công");
              panelSinhVien.unblock();

              // window.location = this.router;
            }
          })
          .catch((error) => {
            if (error.response.status == 422) {
              Vue.set(this.table, "errors", error.response.data.errors);
            }
            panelSinhVien.unblock();
          });
      }
    },
    actionShowSelectFile: function () {
      document.getElementById("import-file").click();
    },
    actionSelectFile: function (event) {
      if (event.target.files.length) {
        consumer
          .uploadFile(event.target.files[0], this.lh_nienche)
          .then((response) => {
            // console.log(response.data[0].rows);
            // console.log(this.editForm.model);

            this.editForm.model.data = this.editForm.model.data.map((item) => {
              const foundItem = response.data[0].rows.find(
                (updatedItem) => updatedItem.sv_ma === item.sv_ma
              );
              if (foundItem) {
                return { ...item, ...foundItem };
              }
              return item;
            });

            AlertBox.Notify.Success("Bạn vừa chọn từ file excel thành công!");

            // this.listSheet.list = response.data;
            //this.preImportDiem();

            if (this.listSheet.list.length > 1) {
              chonSheetModal.show();
            }
            if (this.listSheet.list.length == 1) {
              this.selectSheet(0);
            }

            document.getElementById("import-file").value = "";
          })
          .catch((error) => {
            AlertBox.Notify.Failure("Nhập dữ liệu thất bại");
            console.log(error);
          });
      }
    },
    deleteItem: function (index) {
      this.table.list.splice(index, 1);
      AlertBox.Notify.Success("Xóa thành công");
    },
    changeFilter: function () {
      console.log("ok");
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
    reloadSinhVien: function () {
      let lh_id = this.lh_id;
      // let mh_id = this.editForm.model.mh_id;
      // let dotThi = this.editForm.model.dt_id;

      //  894,
      let mh_id_loai0 = [885, 887, 886];
      let mh_id_loai1 = 894;
      let dt_id = this.dt_id;
      const diemBySvId = {};

      panelDiem.block();
      // Điểm môn thi loại 0
      // mh_id_loai0.forEach((currentMhId, index) => {
      //   consumer.getBangDiem(lh_id, currentMhId, dt_id).then((data) => {
      //     data.data.forEach((sv) => {
      //       const svId = sv.sv_id;

      //       if (!diemBySvId.hasOwnProperty(svId)) {
      //         diemBySvId[svId] = {};
      //       }

      //       switch (index) {
      //         case 0:
      //           // Môn chính trị
      //           diemBySvId[svId].chinhtri = sv.svd_first;
      //           break;
      //         case 1:
      //           // Môn lý thuyết
      //           diemBySvId[svId].lythuyet = sv.svd_first;
      //           break;
      //         case 2:
      //           // Môn thực hành
      //           diemBySvId[svId].thuchanh = sv.svd_first;
      //           break;
      //         default:
      //           break;
      //       }
      //     });

      //     // console.log("diemBySvId:", diemBySvId);
      //   });
      // });

      // console.log("diemBySvId: ", diemBySvId)

      var ds_sinhvienLoai1;
      // Khởi tạo biến đếm
      var completedRequests = 0;
      mh_id_loai0.forEach((currentMhId, index) => {
        consumer.getBangDiem(lh_id, currentMhId, dt_id).then((data) => {
          data.data.forEach((sv) => {
            const svId = sv.sv_id;

            if (!diemBySvId.hasOwnProperty(svId)) {
              diemBySvId[svId] = {};
            }

            console.log("sv.svd_first", sv.svd_first)
            switch (index) {
              case 0:
                // Môn chính trị
                diemBySvId[svId].chinhtri = sv.svd_first;
                break;
              case 1:
                // Môn lý thuyết
                diemBySvId[svId].lythuyet = sv.svd_first;
                break;
              case 2:
                // Môn thực hành
                diemBySvId[svId].thuchanh = sv.svd_first;
                break;
              default:
                break;
            }

            // Gán giá trị diemBySvId cho mảng con của sv_id
            sv.chinhtri = diemBySvId[svId].chinhtri ;
            sv.lythuyet = diemBySvId[svId].lythuyet ;
            sv.thuchanh = diemBySvId[svId].thuchanh ;
          });


          if (completedRequests === 0) {
            ds_sinhvienLoai1 = data;
          } else {
            ds_sinhvienLoai1.data.push(...data.data);
          }

          // Tăng biến đếm khi một yêu cầu hoàn thành
          completedRequests++;
          // Kiểm tra nếu tất cả các yêu cầu đã hoàn thành
          if (completedRequests === mh_id_loai0.length) {
            // Gán giá trị data cho this.editForm.model khi tất cả các yêu cầu đã hoàn thành
            this.editForm.model = ds_sinhvienLoai1;
            panelDiem.unblock();
          }
        });
      });
    },
    loadLopHoc: function () {
      var vm = this;
      // Load thông tin lớp học
      consumer.getLopHoc(this.lh_id).then((lopHoc) => {
        this.editForm.lopHoc = lopHoc;

        // Load danh sách học kỳ
        this.reloadSinhVien();
      });

      let mh_id = 894;
      consumer.getMonHoc(mh_id).then((monHoc) => {
        this.editForm.monHoc = monHoc;
      });
    },
    actionSave: function () {
      var vm = this;

      var data = {
        lh_id: this.lh_id,
        mh_id: [885, 887, 886],
        dt_id: this.dt_id,
        data: this.editForm.model.data,
      };

      // console.log("data: ", data)

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
  },
};
</script>
