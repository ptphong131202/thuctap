Tuần 2: 
* Phong 
    - Thực hiện chức năng thống kê sau giao diện đăng nhập (dashboard-canbo):
        + Thống kê số lượng lớp học theo niên khóa
        + Thống kê chương trình đào tạo theo hệ đào tạo (Cao đẳng, Trung cấp)
        + Thống kê ngành, nghề đào tạo theo hệ đào tạo (Cao đẳng, Trung cấp)
        + Thống kê đợt thi, đợt xét tốt nghiệp theo năm
        + Thống kê số lượng sinh viên đang học, tạm nghỉ và tốt nghiệp
    - Tìm hiểu Zalo OA: Thực hiện liên kết phần mềm qua Zalo OA, thông báo điểm học tập qua zalo cho sinh viên (Chưa hoàn thành )

    * Chỉnh sửa
    - file dashboardController : hàm indexCanBo()
    - thêm file zaloController.php
    - Thêm thư mục app/services/zaloService.php
    - Chỉnh sửa file web.php: Thêm router thông báo điểm qua zalo
    - Chỉnh sửa file dashboard-canbo.blade.php

* Dinh 
    - Gộp chức năng "Ngành nghề đào tạo" và "Môn học" thành 1: Vào ngành nghề đào tạo -> chọn chi tiết -> quản lý môn học theo từng ngành nghề.
    - Quản lý lớp học -> Cột sinh viên -> Nút sinh viên: Sửa lại chỉ hiển thị sinh viên còn đang học (chưa có QĐ Xóa tên).
    - Quản lý lớp học: Chọn Edit: Hiển thị số "Thành lập theo quyết định" sửa thành mẫu cấu trúc: QĐ số 123/QĐ-CĐCĐNB ngày 05/4/2024 về việc ....

    * Chỉnh sửa
    - file LopHocComponent.vue : 
        + Chỉnh sửa số lượng sinh viên hiển thị (vd: 24/26 sinh viên).
        + Chỉnh sửa "Thành lập theo quyết định" hiển thị "QĐ số 123/QĐ-CĐCĐNB ngày 05/4/2024 về việc ...."
    - file LopHocController: chỉnh sửa hàm "paginate" để đếm số lượng sinh viên còn học.
    - file web.php: thêm router để hiển thị danh sách các môn học theo ngành nghề.
    - Thêm file NganhNgheMonHoc.vue và file nganhnghe_monhoc.blade.php để hiển thị danh sách ngành nghề.
    - file vue-component: thêm đường dẫn tới file NganhNgheMonHoc.vue
    - file NganhNgeController:
        + Thêm hàm "monhoc" để điều hướng về danh sách môn học
        + Chỉnh sửa hàm "paginate" để danh sách nghành nghề trả về kèm theo url điều hướng sang trang danh sách môn học