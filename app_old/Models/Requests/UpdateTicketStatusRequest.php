<?php

namespace App\Http\Requests;

use App\Models\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketStatusRequest extends FormRequest
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
        $rules = TicketStatus::$rules;
        $rules['name'] = 'required|unique:ticket_statuses,name,'.$this->route('ticketStatus')->id;

        return $rules;
    }
}
