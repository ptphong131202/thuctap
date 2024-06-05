<!-- NGUYEN PHU DINH -->
<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Ngành: {{ nganhNghe.nn_ten }}
                <small v-if="hdt_id == 5"> Hệ trung cấp </small>
                <small v-else> Hệ cao đẳng </small>
            </h1>
            <ol class="breadcrumb">
                <li>
                    <a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a>
                </li>
                <li>
                    <a :href="'http://localhost/cea-2.0/public' + parent_url"
                        >Ngành nghề</a
                    >
                </li>
                <li class="active">{{ nganhNghe.nn_ma }}</li>
            </ol>
        </section>

        <section class="content">
            <div style="margin-bottom: 6px">
                <a
                    href="javascript:void(0);"
                    class="btn btn-danger btn-flat"
                    title="Thêm mới"
                    v-on:click="create()"
                >
                    <i class="fa fa-plus"></i> Thêm mới
                </a>
                <a v-bind:href="excel" class="btn btn-success btn-flat"
                    ><i class="fa fa-file-excel-o"></i> Thêm môn học bằng
                    Excel</a
                >

                <a
                    :href="'http://localhost/cea-2.0/public' + parent_url"
                    class="btn btn-default"
                >
                    <i class="fa fa-share"></i> Trở về danh sách
                </a>
            </div>
            <div class="row semester-item">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-widget">
                        <div class="box-body table-responsive">
                            <table
                                class="table table-hover table-striped table-bordered no-margin"
                            >
                                <thead>
                                    <tr>
                                        <th class="text-center w-3">STT</th>
                                        <th>Mã</th>
                                        <th>Môn học</th>
                                        <th class="w-10">Số tín chỉ</th>
                                        <th class="w-10">Số tiết/giờ</th>
                                        <th class="w-10">Tích lũy</th>
                                        <th class="w-90-p text-center">
                                            Hành động
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            mh, semesterIndex
                                        ) in listMonHoc.original"
                                        :key="semesterIndex"
                                    >
                                        <td class="text-center">
                                            {{ semesterIndex + 1 }}
                                        </td>
                                        <td>{{ mh.mh_ma }}</td>
                                        <td>
                                            <span v-if="!mh.bang_diem_exists">{{
                                                mh.mh_ten
                                            }}</span>
                                        </td>
                                        <td>{{ mh.mh_sodonvihoctrinh }}</td>
                                        <td>{{ mh.mh_sotiet }}</td>
                                        <td>
                                            {{ mh.mh_tichluy ? "Có" : "Không" }}
                                        </td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn bg-orange btn-sm pull-left"
                                                title="Thay đổi"
                                                v-if="
                                                    !mh.bang_diem_exists &&
                                                    mh.deleted_at == null
                                                "
                                                v-on:click="edit(mh.mh_id)"
                                            >
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm pull-right"
                                                title="Xóa"
                                                v-if="
                                                    !mh.bang_diem_exists &&
                                                    mh.deleted_at == null
                                                "
                                                v-on:click="
                                                    destroy(mh.mh_id, mh.mh_ten)
                                                "
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-primary btn-sm pull-right"
                                                title="Khôi phục"
                                                v-if="
                                                    !mh.bang_diem_exists &&
                                                    mh.deleted_at != null
                                                "
                                                v-on:click="
                                                    restore(mh.mh_id, mh.mh_ten)
                                                "
                                            >
                                                <i
                                                    class="fa fa-undo"
                                                    aria-hidden="true"
                                                ></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr
                                        v-if="
                                            listMonHoc.original == null ||
                                            listMonHoc.original.length == 0
                                        "
                                    >
                                        <td colspan="100" class="text-center">
                                            {{
                                                listMonHoc.original != null
                                                    ? "Không tìm thấy dữ liệu"
                                                    : "Đang tải"
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <a
                :href="'http://localhost/cea-2.0/public' + parent_url"
                class="btn btn-default"
            >
                <i class="fa fa-share"></i> Trở về danh sách
            </a>
        </section>
        <!-- /.content -->

        <div class="modal fade" id="mon-hoc-edit-modal">
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
                        <h4 class="modal-title">Thêm môn học</h4>
                    </div>
                    <div class="modal-body">
                        <form-group :errors="editForm.errors" :field="'hdt_id'">
                            <label>Hệ đào tạo</label>
                            <select2
                                v-model="editForm.model.hdt_id"
                                :options="listHeDaoTao.select2Form"
                            ></select2>
                        </form-group>
                        <form-group :errors="editForm.errors" :field="'nn_id'">
                            <label>Ngành, nghề</label>
                            <select2
                                v-model="editForm.model.nn_id"
                                :options="listNganhNghe.select2"
                            ></select2>
                        </form-group>
                        <form-group :errors="editForm.errors" :field="'mh_ma'">
                            <label>Mã môn học</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="editForm.model.mh_ma"
                            />
                        </form-group>
                        <form-group :errors="editForm.errors" :field="'mh_ten'">
                            <label>Tên môn học</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="editForm.model.mh_ten"
                            />
                        </form-group>
                        <form-group
                            :errors="editForm.errors"
                            :field="'mh_sodonvihoctrinh'"
                        >
                            <label>Số tín chỉ</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="editForm.model.mh_sodonvihoctrinh"
                            />
                        </form-group>
                        <form-group
                            :errors="editForm.errors"
                            :field="'mh_sotiet'"
                        >
                            <label>Số tiết/giờ</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="editForm.model.mh_sotiet"
                            />
                        </form-group>
                        <form-group
                            :errors="editForm.errors"
                            :field="'mh_sotiet'"
                        >
                            <label>Loại môn</label>
                            <select
                                class="form-control"
                                v-model="editForm.model.mh_loai"
                            >
                                <option value="1">Bình thường</option>
                                <option value="2">Thi chính trị</option>
                                <option value="3">
                                    Thi tốt nghiệp thực hành
                                </option>
                                <option value="4">
                                    Thi tốt nghiệp lý thuyết
                                </option>
                                <option value="5">
                                    Thi tốt nghiệp khóa luận
                                </option>
                            </select>
                        </form-group>
                        <form-group
                            :errors="editForm.errors"
                            :field="'mh_tichluy'"
                        >
                            <div class="checkbox">
                                <label>
                                    <input
                                        type="checkbox"
                                        v-model="editForm.model.mh_tichluy"
                                    />
                                    Tính tích lũy
                                </label>
                            </div>
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
    </div>
</template>

<script>
import draggable from "vuedraggable";
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
    getNganhNge: function (nn_id) {
        const url = "http://localhost/cea-2.0/public/api/nganh-nghe/" + nn_id;
        return axios.get(url).then((response) => response.data);
    },

    getDanhSachMonHoc: function (hdt_id, nn_id) {
        const url =
            "http://localhost/cea-2.0/public/api/mon-hoc/all?hdt_id=" +
            hdt_id +
            "&nn_id=" +
            nn_id;

        return axios.get(url).then((response) => response.data);
    },
    getMonHoc: function (mh_id) {
        const url = "http://localhost/cea-2.0/public/api/mon-hoc/" + mh_id;
        return axios.get(url).then((response) => response.data);
    },
    getListHeDaoTao: function () {
        const url = "http://localhost/cea-2.0/public/api/he-dao-tao/all";
        return axios.get(url).then((response) => response.data);
    },
    getListNganhNghe: function (hdt_id) {
        const url =
            "http://localhost/cea-2.0/public/api/nganh-nghe/all?hedaotao=" +
            hdt_id;
        return axios.get(url).then((response) => response.data);
    },
    save: function (formData) {
        if (formData.mh_id == null) {
            return axios.post(
                "http://localhost/cea-2.0/public/api/mon-hoc",
                formData
            );
        } else {
            return axios.put(
                "http://localhost/cea-2.0/public/api/mon-hoc/" + formData.mh_id,
                formData
            );
        }
    },
    destroy: function (mh_id) {
        return axios.delete(
            "http://localhost/cea-2.0/public/api/mon-hoc/" + mh_id
        );
    },
    restore: function (mh_id) {
        return axios.put(
            "http://localhost/cea-2.0/public/api/mon-hoc/restore/" + mh_id
        );
    },
};

export default {
    props: ["excel", "nn_id", "hdt_id", "parent_url"],
    mounted() {
        this.init();
    },
    data() {
        return {
            nganhNghe: {},
            editForm: {
                reference: Object,
                model: Object,
                errors: Object,
            },
            listMonHoc: {
                original: [],
                select2: [],
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
        loadDanhSachMonHoc: function (hdt_id, nn_id) {
            consumer.getDanhSachMonHoc(hdt_id, nn_id).then((data) => {
                data.sort((a, b) => a.mh_loai - b.mh_loai);
                this.listMonHoc.original = data;
            });
        },
        loadNganhNghe: function (nn_id) {
            consumer.getNganhNge(nn_id).then((data) => {
                this.nganhNghe = data;
                this.loadDanhSachMonHoc(data.hdt_id, data.nn_id);
            });
            consumer.getListHeDaoTao().then((data) => {
                let dataSelect = data.map((item) => {
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

            consumer.getListNganhNghe().then((data) => {
                this.listNganhNghe.select2 = data.map((item) => {
                    return {
                        id: item.nn_id,
                        hdt_id: item.hdt_id,
                        text: item.nn_ten + " - " + item.he_dao_tao.hdt_ten,
                    };
                });
            });
        },
        init: function () {
            this.loadNganhNghe(this.nn_id);
        },
        resetEditForm: function () {
            this.editForm = {
                reference: Object,
                model: {
                    hdt_id: this.hdt_id,
                    nn_id: this.nn_id,
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
            }
        },
        store: function () {
            var vm = this;
            consumer
                .save(this.editForm.model)
                .then((response) => {
                    if (response.status == 200) {
                        vm.resetEditForm();
                        vm.init();
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
        edit: function (mh_id) {
            this.resetEditForm();
            consumer.getMonHoc(mh_id).then((data) => {
                this.editForm.model = data;
                this.editForm.reference = { ...this.editForm.model };
                editModal.show();
            });
        },
        update: function () {
            const newMh_tichluy =
                typeof this.editForm.model.mh_tichluy === "boolean"
                    ? this.editForm.model.mh_tichluy
                    : false;
            const newModel = {
                ...this.editForm.model,
                mh_tichluy: newMh_tichluy,
            };
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
                        Vue.set(
                            vm.editForm,
                            "errors",
                            error.response.data.errors
                        );
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
                            AlertBox.Notify.Failure(
                                "Không thể xóa môn học này!"
                            );
                        });
                },
                function () {}
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
                            AlertBox.Notify.Success(
                                "Khôi phục môn học thành công"
                            );
                        })
                        .catch((error) => {
                            vm.reloadList();
                            AlertBox.Notify.Failure(
                                "Khôi phục môn học thất bại"
                            );
                        });
                },
                function () {}
            );
        },
    },
};
</script>
