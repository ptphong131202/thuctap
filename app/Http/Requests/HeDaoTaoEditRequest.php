<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HeDaoTaoEditRequest extends FormRequest
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
            'hdt_ten' => 'required|max:255|unique:qlsv_hedaotao,hdt_ten,' . request()->route('he_dao_tao') . ',hdt_id',
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
            'hdt_ten' => 'Hệ đào tạo',
        ];
        return $attributes;
    }
}
