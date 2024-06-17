<?php

namespace App\Enums;

use App\Base\Enum;

class Permission extends Enum
{
    /**
     * Quản trị có tất cả quyền
     */
    const ADMINISTRATORS = 'administrators';

    const USERS = 'admin.users';

    const COMMON_INDEX = "admin.index";

    const SCORE = "admin.score";

    const STUDENT = "student";

    const TRUNGCAP = "trungcap";
    
    const XNKD = "admin.log"; // sửa

    const CAODANG = "caodang";

    public static function getPermisstion() {
        return [
            self::ADMINISTRATORS => 'Quyền quản trị',
            self::USERS => 'Quản lý người dùng',
            self::COMMON_INDEX => 'Quản lý danh mục',
            self::SCORE => 'Quản lý điểm',
            self::STUDENT => 'Sinh viên',
            self::TRUNGCAP => 'Trung cấp',
            self::CAODANG => 'Cao đẳng',
            self::XEMDIEM => 'Xem điểm',
            self::XNKD => 'Xem nhật ký điểm', /// đã sửa 
            self::NHAPRENLUYEN => 'Nhập điểm rèn luyện',
        ];
    }
}