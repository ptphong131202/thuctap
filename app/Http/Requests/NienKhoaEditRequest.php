<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NienKhoaEditRequest extends FormRequest
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
            'nk_ten' => 'required|max:255|unique:qlsv_nienkhoa,nk_ten,' . request()->route('nien_khoa') . ',nk_id',
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
            'nk_ten' => 'Niên khóa',
        ];
        return $attributes;
    }
}
