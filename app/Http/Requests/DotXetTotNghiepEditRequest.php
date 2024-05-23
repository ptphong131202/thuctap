<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DotXetTotNghiepEditRequest extends FormRequest
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


        // Nếu qd_id không phải là -1, thêm rule kiểm tra qd_ma
        if ($this->input('qd_id') == -1) {
            $rules['dxtn_ten'] = 'required|max:255' . request()->route('dot_xet_tot_nghiep') . ',dxtn_ten';
        } else {
            $rules = [
                //'dxtn_ten' => 'required|max:255|unique:qlsv_dotxettotnghiep,dxtn_ten,' . request()->route('dot_xet_tot_nghiep') . ',dxtn_id',
                'dxtn_ten' => 'required|max:255' . request()->route('dot_xet_tot_nghiep') . ',dxtn_ten',
                'qd_ma' => 'required|max:60' . request()->route('dot_xet_tot_nghiep') . ',qd_ma',
                'qd_ten' => 'required|max:255' . request()->route('dot_xet_tot_nghiep') . ',qd_ten',
                'qd_ngay' => 'required|date',
                'qd_id' => 'required|max:60|unique:qlsv_dotxettotnghiep,qd_id,' . request()->route('dot_xet_tot_nghiep') . ',qd_id',
            ];
        }

        return $rules;

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
            'dxtn_ten' => 'tên đợt xét tốt nghiệp',
            'qd_ma' => 'mã quyết định',
            'qd_ten' => 'nội dung quyết định',
            'qd_ngay' => 'ngày quyết định',
            'qd_id' => 'quyết định ID',

        ];
        return $attributes;
    }
}
