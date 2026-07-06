<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\VehicleRental;

class UpdateVehicleRentalRequest extends FormRequest
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


    public function rules()
    {
        $id = $this->route('rental')->id;  // Get the id from the route (assuming it's passed in the route)
        return VehicleRental::rules($id);  // Pass the id to the VehicleRental::rules method for proper validation
    }
}
