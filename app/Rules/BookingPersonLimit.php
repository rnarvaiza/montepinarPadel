<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;
use App\Models\Booking;

class BookingPersonLimit implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $user_id;
    public function __construct($id)
    {
        $this->user_id=$id;
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
        $beginningHour = Carbon::create($value);
        $beginningHour->subUnitNoOverflow('hour', 25, 'day');
        $finishingHour =  Carbon::create($value);
        $finishingHour->addUnitNoOverflow('hour', 25, 'day');
        return Booking::where('id','=',$this->user_id)->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
