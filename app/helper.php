<?php

/**
 * Lấy người đung đang đăng nhập
 */
if (! function_exists('user')) {
    function user() {
        return auth()->guard('web');
    }
}

/**
 * Get bitcore config
 */
if (! function_exists('__c')) {
    function __c($config, $default = null)
    {
        return config("core.$config", $default);
    }
}

/**
 * Giải mã
 */
if (! function_exists('param_decode')) {
    function param_decode($param)
    {
        return base64_decode($param);
    }
}

/**
 * Mã hóa
 */
if (! function_exists('param_encode')) {
    function param_encode($param)
    {
        return base64_encode($param);
    }
}

if (! function_exists('is_super_admin')) {
    function is_super_admin()
    {
        return auth()->user()->user_id === __c('super_admin.user_id');
    }
}

if (! function_exists('hasPermission')) {
    function hasPermission($permission) {
        if ($permission == \App\Enums\Permission::STUDENT) {
            return auth()->user()->hasPermission($permission);
        }
        return is_super_admin() || auth()->user()->hasPermission($permission);
    }
}

if (! function_exists('getHocLuc')) {
    function getHocLuc($diem) {
        if ($diem >= 9.0) {
            return 'Xuất sắc';
        } else if ($diem <= 8.9 && $diem >= 8.0) {
            return 'Giỏi';
        } else if ($diem <= 7.9 && $diem >= 7.0) {
            return 'Khá';
        } else if ($diem <= 6.9 && $diem >= 6.0) {
            return 'Trung bình khá';
        } else if ($diem <= 5.9 && $diem >= 5.0) {
            return 'Trung bình';
        } else if ($diem <= 4.9) {
            return 'Yếu';
        }
    }
}

if(! function_exists('numberToRoman')) {
    function numberToRoman($number) {
        if($number == 1){
            return 'I';
        }else if($number == 2){
            return 'II';
        }else if($number == 3){
            return 'III';
        }else if($number == 4){
            return 'IV';
        }
    }
}

if (! function_exists('assetv')) {
    function assetv($str) {
        return asset($str) . '?v' . __c('app_version');
    }
}


