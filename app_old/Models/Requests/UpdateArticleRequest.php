<?php

namespace App\Http\Requests;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = Article::$rules;
        $rules['subject'] = 'required|unique:articles,subject,'.$this->route('article')->id;
        $rules['image'] = 'nullable|mimes:jpeg,jpg,png|max:2000';

        return $rules;
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return Article::$messages;
    }
}
