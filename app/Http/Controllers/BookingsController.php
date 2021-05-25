<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Rules\BookingOverlap;
use Carbon\Carbon;
use App\Rules\BookingAvailableScheudle;
use App\Rules\BookingWeeklyLimit;
use App\Rules\BookingTimeLimit;
use App\Models\User;

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()->bookings();
        return view('dashboard', compact('bookings'));
    }

    //On add function we call Booking::all() in order to show all bookings and help user not to overlap with other bookings.

    public function add()
    {
        $bookings = Booking::all();

        return view('add')->with('bookings', $bookings);

    }
    //The order of validations to avoid server overload are the detailed below.

    public function create(Request $request)
    {

        $this->validate($request, [
            'start_time' => ['required', 'date', 'after:now', new BookingAvailableScheudle, new BookingOverlap() ,new BookingTimeLimit($request->end_time)],
            'end_time' => ['required', 'date', 'after:start_time', new BookingOverlap(), new BookingAvailableScheudle, new BookingWeeklyLimit()]
        ]);

        $booking = new Booking();
        $booking->user_id = auth()->user()->id;
        $booking->start_time = $request->start_time;
        $booking->end_time = $request->end_time;

        $beginningHour = Carbon::create($booking->start_time);
        $beginningHour->subUnitNoOverflow('hour', 25, 'day');
        $beginningHour->addUnitNoOverflow('hour', 9, 'day');
        $finishingHour =  Carbon::create($booking->end_time);
        $finishingHour->addUnitNoOverflow('hour', 25, 'day');
        $finishingHour->subUnitNoOverflow('hour', 1, 'day');

        $userHasBooked = Booking::where('user_id','=',$booking->user_id)->where('start_time','>=', $beginningHour)->where('end_time', '<=', $finishingHour)->count() > 0;

        if(!$userHasBooked){
            $booking->save();
        }else{
            $this->message();
        }

        return redirect('/dashboard');
    }

    public function message()
    {
        return 'Superado límite de reservas diarias.';
    }

    //TODO Cómo puedo recuperar en el edit TODAS las reservas igual que en el add?

    public function edit(Booking $booking)
    {

        if (auth()->user()->id == $booking->user_id) {
            return view('edit', compact('booking') );
        } else {
            return redirect('/dashboard');
        }
        /*
        $bookings = Booking::all();

        return view('add')->with('bookings', $bookings);
        */
    }



    public function update(Request $request, Booking $booking)
    {
        if (isset($_POST['delete'])) {
            $booking->delete();
            return redirect('/dashboard');
        } else {
            $this->validate($request, [
                'start_time' => ['required', 'date', 'after:now',  new BookingAvailableScheudle, new BookingTimeLimit($request->end_time)],
                'end_time' => ['required', 'date', 'after:start_time', new BookingAvailableScheudle, new BookingWeeklyLimit]
            ]);
            $booking->start_time = $request->start_time;
            $booking->end_time = $request->end_time;
            $beginningHour = Carbon::create($booking->start_time);
            $beginningHour->subUnitNoOverflow('hour', 25, 'day');
            $beginningHour->addUnitNoOverflow('hour', 9, 'day');
            $finishingHour =  Carbon::create($booking->end_time);
            $finishingHour->addUnitNoOverflow('hour', 25, 'day');
            $finishingHour->subUnitNoOverflow('hour', 1, 'day');

            $userHasBooked = Booking::where('user_id','=',$booking->user_id)->where('start_time','>=', $beginningHour)->where('end_time', '<=', $finishingHour)->where('id', '!=', $request->id)->count() >= 1;
            $rangeAlreadyBooked = Booking::where('start_time', '<=', $booking->start_time)->where('end_time', '>=', $booking->end_time)->count() == 0;
            if(!$userHasBooked || !$rangeAlreadyBooked){
                $booking->save();
            }
            return redirect('/dashboard');
        }
    }
}
