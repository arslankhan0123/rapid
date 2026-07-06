<?php

namespace App\Http\Requests;

use App\Models\ProductGroup;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductGroupRequest extends FormRequest
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
        $rules = ProductGroup::$rules;
        $rules['name'] = 'required|unique:item_groups,name,'.$this->route('productGroup')->id;

        return $rules;
    }
}
