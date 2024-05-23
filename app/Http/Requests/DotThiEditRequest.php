<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DotThiEditRequest extends FormRequest
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
            // 'dt_ten' => 'required|max:255|unique:qlsv_dotthi,dt_ten,' . request()->route('dot_thi') . ',dt_id',
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
            // 'dt_ten' => 'Tên đợt thi',
        ];
        return $attributes;
    }
}
