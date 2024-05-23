<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NganhNgheEditRequest extends FormRequest
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
            'nn_ma' => 'required|max:255|unique:qlsv_nganhnghe,nn_ma,' . request()->route('nganh_nghe') . ',nn_id',
            'nn_ten' => 'required|max:255',
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
            'nn_ma' => 'Mã ngành, nghề',
            'nn_ten' => 'Tên ngành, nghề',
            'hdt_id' => 'Hệ đào tạo',
        ];
        return $attributes;
    }
}
