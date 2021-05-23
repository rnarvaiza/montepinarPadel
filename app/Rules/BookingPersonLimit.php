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
    public $start_time;
    public function __construct($startTime)
    {
        $this->start_time=$startTime;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */

    /**
     * TODO Validación pasa siempre, parece que no llega el user_id
     */
    public function passes($attribute, $value)
    {
        $beginningHour = Carbon::create($this->start_time);
        $beginningHour->subUnitNoOverflow('hour', 25, 'day');
        $finishingHour =  Carbon::create($this->start_time);
        $finishingHour->addUnitNoOverflow('hour', 25, 'day');

        return Booking::where('id','=',$value)->where('start_time','>=', $beginningHour)->where('end_time', '<=', $finishingHour)->count() == 0;
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
