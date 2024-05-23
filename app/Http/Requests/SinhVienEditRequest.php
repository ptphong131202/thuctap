<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SinhVien;

class SinhVienEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (intval(request()->route('sinh_vien'))) {
            return $this->updateRules();
        }
        return $this->createRules();
    }

    private function createRules()
    {
        return [
            'sv_ma' => 'required|max:255|min:4|unique:users,username,0,user_id',
            'password' => 'required|max:255|min:6',
            'sv_ho' => 'required|max:255',
            'sv_ten' => 'required|max:255',
            'sv_ngaysinh' => 'required',
            'qd_ma' => 'required_if:qd_id,==,0|max:255|unique:qlsv_quyetdinh,qd_ma',
            'qd_ten' => 'required_if:qd_id,0|max:255',
            'qd_ngay' => 'required_if:qd_id,0',
            'lh_id' => 'required',
        ];
    }

    private function updateRules()
    {
        $userId = SinhVien::find(request()->route('sinh_vien'))->user_id;
        return [
            'sv_ma' => 'required|max:255|min:4|unique:users,username,' . $userId . ',user_id',
            'password' => 'max:255|min:6',
            'sv_ho' => 'required|max:255',
            'sv_ten' => 'required|max:255',
            'sv_ngaysinh' => 'required',
            'qd_ma' => 'required_if:qd_id,==,0|max:255|unique:qlsv_quyetdinh,qd_ma',
            'qd_ten' => 'required_if:qd_id,0|max:255',
            'qd_ngay' => 'required_if:qd_id,0',
            'lh_id' => 'required',
        ];
    }

    /**
     * Attr named
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function attributes()
    {
        $attributes = [
            'sv_ten' => 'Tên',
            'sv_ho' => 'Họ',
            'sv_ma' => 'Mã sinh viên',
            'sv_ngaysinh' => 'Ngày sinh',
            'password' => 'Mật khẩu',
            'email' => 'Email',
            'qd_ma' => 'Số quyết định',
            'qd_ten' => 'Tên quyết định',
            'qd_ngay' => 'Ngày quyết định',
            'qd_id' => 'Quyết định',
            'lh_id' => 'Lớp học',
        ];
        return $attributes;
    }

    public function messages()
    {
        return [
            'qd_ma.required_if' => 'Vui lòng nhập số quyết định',
            'qd_ten.required_if' => 'Vui lòng nhập tên quyết định',
            'qd_ngay.required_if' => 'Vui lòng nhập ngày',
            'lh_id.required' => 'Vui lòng chọn lớp học',
        ];
    }
}
