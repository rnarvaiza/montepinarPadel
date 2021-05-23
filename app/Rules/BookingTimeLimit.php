<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class BookingTimeLimit implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $end_time;
    public function __construct($end_time)
    {
        $this->end_time = $end_time;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $carbonStartDate = Carbon::parse($value);
        $carbonEndDate = Carbon::parse($this->end_time);
        return $carbonEndDate->diffInMinutes($carbonStartDate)<=120;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Reserva superior a dos horas.';
    }
}
