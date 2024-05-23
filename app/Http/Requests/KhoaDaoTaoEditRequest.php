<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KhoaDaoTaoEditRequest extends FormRequest
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
            'kdt_ma' => 'required|max:255|unique:qlsv_khoadaotao,kdt_ma,' . request()->route('khoa_dao_tao') . ',kdt_id',
            'kdt_ten' => 'required|max:255',
            'kdt_khoa' => 'required|max:255|integer',
            'kdt_he' => 'required|max:255',
            'nn_id' => 'required',
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
            'kdt_ma' => 'Mã chương trình đào tạo',
            'kdt_ten' => 'Tên chương trình đào tạo',
            'kdt_khoa' => 'Khóa',
            'kdt_he' => 'Hệ',
            'nn_id' => 'Ngành, nghề',
            'hdt_id' => 'Hệ đào tạo',
        ];
        return $attributes;
    }
}
