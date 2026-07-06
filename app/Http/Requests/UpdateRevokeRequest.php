<?php

namespace App\Http\Requests;

use App\Models\Revoke;
use App\Models\Termination;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRevokeRequest extends FormRequest
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
    //     $revokeId = $this->route('revoke');

    //     return [
    //         'reason' => 'nullable|string',
    //         'date' => 'required|date',
    //         'status' => 'boolean'
    //     ];
    // }
    public function rules()
    {
        // Get the revoke instance from route parameter
        $revoke = $this->route('revoke');

        return [
            'reason' => 'nullable|string',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($revoke) {
                    if ($revoke && $revoke->termination && $revoke->termination->date) {
                        $revokeDate = Carbon::parse($value);
                        $terminationDate = Carbon::parse($revoke->termination->date);

                        if ($revokeDate->lessThan($terminationDate)) {
                            $fail('The revoke date cannot be before the termination date (' . $terminationDate->format('d-m-Y') . ').');
                        }
                    }
                }
            ],
            'status' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'The revoke date is required.',
            'date.date' => 'The revoke date must be a valid date.',
        ];
    }

    public function attributes()
    {
        return [
            'date' => 'revoke date',
        ];
    }
}
