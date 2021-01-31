<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Actions\Fortify\PasswordValidationRules;

class UserRequest extends FormRequest
{
    use PasswordValidationRules; 
    
    public function authorize()
    {
        return Auth::check(); 
    }

   
    public function rules()
    {
        return [
            'name' => 'string|required|max:255',
            'email' => 'string|required|max:255|email|unique:users',
            'password' => $this->passwordRules(),
            'address'=>'string|required',
            'roles'=>'string|required|max:255|in:ADMIN,USER',
            'houseNumber'=>'string|required|max:255',
            'phoneNumber'=>'string|required|max:255',
            'city'=>'string|required|max:255',

        ];
    }
}
