<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseDataRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'session' => 'required',
            'session_year' => 'required|string',
            'sub_code' => 'required'
        ];
    }
}
