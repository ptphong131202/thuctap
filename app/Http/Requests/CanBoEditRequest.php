<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CanBoEditRequest extends FormRequest
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
        if (intval(request()->route('user'))) {
            return $this->updateRules();
        }
        return $this->createRules();
    }

    private function createRules()
    {
        $userId = request()->route('user');
        return [
            'username' => 'regex:/(^[a-zA-Z0-9]*$)/|required|max:255|min:4|unique:users,username,' . $userId . ',user_id',
            'password' => 'required|max:255|min:6',
            'email' => 'required|max:255|unique:users,email,' . $userId . ',user_id',
            'cb_ho' => 'max:255',
            'cb_ten' => 'required|max:255',
        ];
    }

    private function updateRules()
    {
        $userId = request()->route('user');
        return [
            'username' => 'regex:/(^[a-zA-Z0-9]*$)/|required|max:255|min:4|unique:users,username,' . $userId . ',user_id',
            'password' => 'max:255|min:6',
            'email' => 'required|max:255|unique:users,email,' . $userId . ',user_id',
            'cb_ho' => 'max:255',
            'cb_ten' => 'required|max:255',
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
            'username' => 'Tài khoản',
            'password' => 'Mật khẩu',
            'email' => 'Email',
            'cb_ho' => 'Họ',
            'cb_ten' => 'Tên',
        ];
        return $attributes;
    }
}
