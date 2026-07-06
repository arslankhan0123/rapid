<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Rules\UniqueHolidayDateRange;
use Illuminate\Support\Facades\Validator;


class Holiday extends Model
{
    use HasFactory;
    protected $table = 'holidays';

    protected $fillable = [
        'name',
        'description',
        'from_date',
        'end_date',
    ];

    // Validation rules
    public static $rules = [
        'name' => 'required|string|max:255|unique:holidays,name',
        'description' => 'nullable|string',
        'from_date' => 'required|date|unique:holidays,from_date',
        'end_date' => 'required|date|after_or_equal:from_date|unique:holidays,end_date',
    ];


    public static function validateHolidayDates($input)
    {
        $rules = self::$rules;

        $validator = Validator::make($input, $rules);

        $validator->after(function ($validator) use ($input) {
            $overlappingHolidays = self::where(function ($query) use ($input) {
                $query->where('from_date', '<=', $input['end_date'])
                    ->where('end_date', '>=', $input['from_date']);
            });

            if (!empty($input['id'])) {
                $overlappingHolidays->where('id', '!=', $input['id']); // Exclude the current record if it's an update
            }

            $conflictingDates = $overlappingHolidays->get(['from_date', 'end_date']);

            if ($conflictingDates->isNotEmpty()) {
                $conflictMessages = $conflictingDates->map(function ($holiday) {
                    return "from {$holiday->from_date} to {$holiday->end_date}";
                })->implode(', ');

                $validator->errors()->add('from_date', "From Date overlaps with: $conflictMessages.");
                $validator->errors()->add('end_date', "End Date overlaps with: $conflictMessages.");
            }
        });

        return $validator;
    }
    // Accessor for formatted from_date
    public function getFormattedFromDateAttribute()
    {
        return Carbon::parse($this->attributes['from_date'])->format('d-M-Y');
    }

    // Accessor for formatted end_date if needed
    public function getFormattedEndDateAttribute()
    {
        return Carbon::parse($this->attributes['end_date'])->format('d-M-Y');
    }
    // Accessor for days count between from_date and end_date
    public function getDaysCountAttribute()
    {
        $fromDate = Carbon::parse($this->attributes['from_date']);
        $endDate = Carbon::parse($this->attributes['end_date']);
        // Calculate the difference in days
        $daysCount = $endDate->diffInDays($fromDate);
        // Return the number of days
        return $daysCount+1;
    }
    protected $appends = ['formatted_from_date', 'formatted_end_date', 'days_count'];
}
