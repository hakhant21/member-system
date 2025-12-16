<?php

namespace DET\Members\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize()
    {
        // Check if user has permission to create members
        return $this->user() && $this->user()->can('member.create');
    }

    public function rules()
    {
        return [
            'email' => 'required|email|unique:members,email',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',

            // Profile Rules (nested array)
            'profile.first_name' => 'nullable|string|max:100',
            'profile.last_name' => 'nullable|string|max:100',
            'profile.date_of_birth' => 'nullable|date|before:today',
            'profile.gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'profile.settings' => 'nullable|json',
        ];
    }
}
