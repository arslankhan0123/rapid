<?php

namespace App\Http\Requests;

use App\Models\Termination;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CreateRevokeRequest extends FormRequest
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
    //         'termination_id' => 'required|exists:terminations,id',
    //         'reason' => 'nullable|string',
    //         'date' => 'required|date',
    //         'status' => 'boolean'
    //     ];
    // }

    // public function messages()
    // {
    //     return [
    //         'termination_id.required' => 'Please select a termination to revoke.',
    //         'termination_id.exists' => 'The selected termination does not exist.',
    //     ];
    // }

    public function rules()
    {
        return [
            'termination_id' => 'required|exists:terminations,id',
            'reason' => 'nullable|string',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $terminationId = $this->input('termination_id');
                    if ($terminationId) {
                        $termination = Termination::find($terminationId);
                        if ($termination && $termination->date) {
                            $revokeDate = Carbon::parse($value);
                            $terminationDate = Carbon::parse($termination->date);

                            if ($revokeDate->lessThan($terminationDate)) {
                                $fail('The revoke date cannot be before the termination date (' . $terminationDate->format('d-m-Y') . ').');
                            }
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
            'termination_id.required' => 'Please select a termination to revoke.',
            'termination_id.exists' => 'The selected termination does not exist.',
            'date.required' => 'The revoke date is required.',
            'date.date' => 'The revoke date must be a valid date.',
        ];
    }

    public function attributes()
    {
        return [
            'termination_id' => 'termination',
            'date' => 'revoke date',
        ];
    }
}
