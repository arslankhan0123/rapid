<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function prepareForValidation()
    {
        $phone = ! empty(request()->get('phone')) ? ('+'.request()->get('prefix_code').request()->get('phone')) : null;
        $this->request->set('phone', removeSpaceFromPhoneNumber($phone));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array The given data was invalid.
     */
    public function rules(): array
    {
        $id = $this->route('member')->id;
        $rules = User::$editRules;
        $rules['email'] = 'required|email|unique:users,email,'.$id.'|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/';
        $rules['phone'] = 'required|unique:users,phone,'.$id;

        return $rules;
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return User::$messages;
    }
}
