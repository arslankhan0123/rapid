<?php

namespace App\Http\Requests;

use App\Models\ArticleGroup;
use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleGroupRequest extends FormRequest
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
        $articlegroup = $this->route('articleGroup');
        $rules = ArticleGroup::$rules;
        $rules['group_name'] = 'required|unique:article_groups,group_name,'.$articlegroup->id;

        return $rules;
    }
}
