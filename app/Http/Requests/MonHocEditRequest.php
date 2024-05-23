<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonHocEditRequest extends FormRequest
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
        return [
            'mh_ma' => 'required|max:255',
            'mh_ten' => 'required|max:255',
            'mh_sodonvihoctrinh' => 'required|integer',
            'nn_id' => 'required|max:255',
            'mh_giangvien' => 'max:255',
            'mh_ghichu' => 'max:255',
            'mh_sotiet' => 'required|integer',
            'hdt_id' => 'required',
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
            'mh_ma' => 'Mã môn học',
            'mh_ten' => 'Tên môn học',
            'mh_sodonvihoctrinh' => 'Số tín chỉ',
            'mh_giangvien' => 'Giảng viên',
            'mh_ghichu' => 'Ghi chú',
            'mh_sotiet' => 'Số tiết/giờ',
            'nn_id' => 'Ngành, nghề',
            'hdt_id' => 'Hệ đào tạo',
        ];
        return $attributes;
    }

    public function messages()
    {
        return [
            'mh_sodonvihoctrinh.max' => 'Số tín chỉ không vượt quá 500',
            'mh_sotiet.max' => 'Số tiết/giờ không vượt quá 500',
        ];
    }
}
