<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'gender' => 'required|string|in:MALE,FEMALE',
            'age' => 'required|integer',
            'phone' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,gif,svg,jpg|max:2048',
            'team_id' => 'required|integer|exists:teams,id',
            'role_id' => 'required|integer|exists:roles,id',
        ];
    }
}
