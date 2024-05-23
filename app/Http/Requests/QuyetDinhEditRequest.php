<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuyetDinhEditRequest extends FormRequest
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
            // 'qd_ma' => 'required|max:255|unique:qlsv_quyetdinh,qd_ma,' . request()->route('quyet_dinh') . ',qd_id',
            'qd_ma' => 'required|max:255',
            'qd_ten' => 'required|max:255',
            'qd_ngay' => 'required',
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
            'qd_ngay' => 'Ngày',
        ];
        return $attributes;
    }
}
