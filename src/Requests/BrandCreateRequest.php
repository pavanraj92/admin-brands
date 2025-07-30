<?php

namespace admin\brands\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [                   
            'name' => 'required|string|min:3|max:100|unique:brands,name',
            'status' => 'required|in:0,1',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
