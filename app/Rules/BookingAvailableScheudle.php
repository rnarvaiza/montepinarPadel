<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class BookingAvailableScheudle implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $inputHour = Carbon::parse($value);
        $beginningHour =  Carbon::create($value);
        $beginningHour->subUnitNoOverflow('hour', 25, 'day');
        $beginningHour->addUnitNoOverflow('hour', 9, 'day');
        $finishingHour =  Carbon::create($value);
        $finishingHour->addUnitNoOverflow('hour', 25, 'day');
        $finishingHour->subUnitNoOverflow('hour', 1, 'day');

        return $inputHour->between($beginningHour, $finishingHour);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'La reserva está fuera del horario permitido.';
    }
}
