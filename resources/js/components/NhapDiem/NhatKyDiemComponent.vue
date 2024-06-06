<template>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Nhật ký điểm
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
                hello bà già
            </div>
        </div>

    </section>
  </div>
</template>

<script>
import axios from 'axios';
const consumer = {
      getNhatkydiem: function () {
        const url = `http://localhost/cea-2.1/public/nhap-diem/nhat-ky-diem`;
        return axios.get(url).then(res => res.data);
    },
    }

export default {
  
    mounted() {
        this.getNhatkydiem();
    },
    methods: {
        getNhatkydiem() {
            consumer.getNhatkydiem().then(res => {
              console.log(res);
            }).catch(error => {
                console.error('Lỗi khi gọi API getNhatkydiem:', error);
            });
        },
        // Các methods khác của component
    }
}
</script>
