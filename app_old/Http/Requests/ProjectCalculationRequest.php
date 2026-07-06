<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectCalculationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    // public function rules()
    // {
    //     return [
    //         'code' => 'required|string|max:50|unique:projects_calculation,code,' . ($this->route('project_calculation') ?? 'NULL') . ',id',
    //         'name' => 'required|string|max:255',
    //         'customer_name' => 'required|string|max:255',
    //         'address' => 'nullable|string|max:500',

    //         // Project Values
    //         'values.*.category' => 'required|string|max:255',
    //         'values.*.amount' => 'required|numeric|min:0',
    //         'values.*.unit' => 'required|string|max:50',
    //         'values.*.quantity' => 'required|integer|min:1',
    //         'values.*.discount' => 'nullable|numeric|min:0',

    //         // Project Expenses
    //         'expenses.*.name' => 'required|string|max:255',
    //         'expenses.*.amount' => 'required|numeric|min:0',
    //         'expenses.*.percentage' => 'nullable|numeric|min:0|max:100',
    //         'expenses.*.unit' => 'required|string|max:50',
    //         'expenses.*.quantity' => 'required|integer|min:1',

    //         // Project Partners
    //         'partners.*.name' => 'required|string|max:255',
    //         'partners.*.amount' => 'required|numeric|min:0',
    //         'partners.*.percentage' => 'nullable|numeric|min:0|max:100',
    //         'partners.*.per_partner' => 'nullable|numeric|min:0',
    //     ];
    // }

    // public function messages()
    // {
    //     return [
    //         'code.required' => 'Project code is required',
    //         'code.unique' => 'Project code already exists',
    //         'name.required' => 'Project name is required',

    //         // Values
    //         'values.*.category.required' => 'Category is required for all value items',
    //         'values.*.amount.required' => 'Amount is required for all value items',
    //         'values.*.quantity.required' => 'Quantity is required for all value items',
    //         'values.*.quantity.integer' => 'Quantity must be an integer for all value items',

    //         // Expenses
    //         'expenses.*.name.required' => 'Name is required for all expense items',
    //         'expenses.*.amount.required' => 'Amount is required for all expense items',
    //         'expenses.*.quantity.required' => 'Quantity is required for all expense items',
    //         'expenses.*.quantity.integer' => 'Quantity must be an integer for all expense items',

    //         // Partners
    //         'partners.*.name.required' => 'Name is required for all partners',
    //         'partners.*.amount.required' => 'Amount is required for all partners',
    //     ];
    // }

    public function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:projects_calculation,code,' . ($this->route('project_calculation') ?? 'NULL') . ',id',
            'name' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',

            // Project Values
            'values.*.category' => 'required|string|max:255',
            'values.*.amount' => 'required|numeric|min:0',
            'values.*.unit' => 'required|numeric|min:0', // Changed from string to numeric
            'values.*.quantity' => 'required|integer|min:1',
            'values.*.discount' => 'nullable|numeric|min:0',

            // Project Expenses
            'expenses.*.name' => 'required|string|max:255',
            'expenses.*.amount' => 'required|numeric|min:0',
            'expenses.*.unit' => 'required|numeric|min:0', // Changed from string to numeric
            'expenses.*.quantity' => 'required|integer|min:1',
            'expenses.*.months' => 'nullable|integer|min:1|max:12',

            'commissions.*.commission_amount' => 'required|numeric|min:0.01',
            'insurances.*.insurance_amount' => 'required|numeric|min:0.01',

            // Project Partners - Remove amount validation since it's calculated
            'partners.*.name' => 'required|string|max:255',
            'partners.*.percentage' => 'required|numeric|min:0|max:100', // Changed from nullable to required
            // Remove 'partners.*.amount' validation since it's calculated
            // Remove 'partners.*.per_partner' validation since it's calculated
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Project code is required',
            'code.unique' => 'Project code already exists',
            'name.required' => 'Project name is required',

            // Values
            'values.*.category.required' => 'Category is required for all value items',
            'values.*.amount.required' => 'Amount is required for all value items',
            'values.*.unit.required' => 'Unit is required for all value items',
            'values.*.unit.numeric' => 'Unit must be a number for all value items',
            'values.*.quantity.required' => 'Quantity is required for all value items',
            'values.*.quantity.integer' => 'Quantity must be an integer for all value items',

            // Expenses
            'expenses.*.name.required' => 'Name is required for all expense items',
            'expenses.*.amount.required' => 'Amount is required for all expense items',
            'expenses.*.unit.required' => 'Unit is required for all expense items',
            'expenses.*.unit.numeric' => 'Unit must be a number for all expense items',
            'expenses.*.quantity.required' => 'Quantity is required for all expense items',
            'expenses.*.quantity.integer' => 'Quantity must be an integer for all expense items',

            // Partners
            'partners.*.name.required' => 'Name is required for all partners',
            'partners.*.percentage.required' => 'Percentage is required for all partners',
        ];
    }
}