<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// file ini berguna untuk melalukan validasi data yang wajib di isi
class RegisterStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // unique di table users
            'password' => 'required|string|min:6'
        ];
    }
}
