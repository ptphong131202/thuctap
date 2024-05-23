<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuyetDinhSinhVienEditRequest extends FormRequest
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
            'qd_ma' => 'required_if:qd_id,==,0|max:255',
            'qd_ten' => 'required_if:qd_id,0|max:255',
            'qd_ngay' => 'required_if:qd_id,0',
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
            'qd_ma' => 'Số quyết định',
            'qd_ten' => 'Tên quyết định',
            'qd_ngay' => 'Ngày quyết định',
            'qd_id' => 'Quyết định',
        ];
        return $attributes;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'qd_ma.required_if' => 'Vui lòng nhập số quyết định',
            'qd_ten.required_if' => 'Vui lòng nhập tên quyết định',
            'qd_ngay.required_if' => 'Vui lòng nhập ngày'
        ];
    }
}
