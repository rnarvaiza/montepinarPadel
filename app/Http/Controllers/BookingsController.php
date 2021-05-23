<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Rules\BookingOverlap;
use Carbon\Carbon;
use App\Rules\BookingAvailableScheudle;
use App\Rules\BookingWeeklyLimit;
use App\Rules\BookingPersonLimit;
use App\Rules\BookingTimeLimit;
use App\Models\User;

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()->bookings();
        return view('dashboard', compact('bookings'));
    }

    /*
    public function collection()
    {
        $bookings = Bookings::all();
        var_dump($bookings);
        exit();
        return view::make('add')->with(compact('bookings'));
    }
    */

    public function add()
    {
        $bookings = Booking::all();

        return view('add')->with('bookings', $bookings);

    }
    //TODO Ordena las validaciones conforme a un criterio lógico, ej. primero las sencillas, luego las más complejas para evitar sobrecarga.
    //TODO Seguir creando las validaciones e ir quitando IFS.
    public function create(Request $request)
    {

        $this->validate($request, [
            'start_time' => ['required', 'date', 'after:now', new BookingPersonLimit($request->user_id), new BookingWeeklyLimit(), new BookingTimeLimit($request->end_time), new BookingOverlap(), new BookingAvailableScheudle],
            'end_time' => ['required', 'date', 'after:start_time', new BookingOverlap(), new BookingAvailableScheudle, new BookingWeeklyLimit]
        ]);

        $booking = new Booking();
        $booking->user_id = auth()->user()->id;
        $booking->start_time = $request->start_time;
        $booking->end_time = $request->end_time;
        /*
        $carbonStartDate = Carbon::parse($request->start_time);
        $carbonEndDate = Carbon::parse($request->end_time);

        $beginningHour = Carbon::create($value);
        $beginningHour->subUnitNoOverflow('hour', 25, 'day');
        $finishingHour =  Carbon::create($value);
        $finishingHour->addUnitNoOverflow('hour', 25, 'day');

        */

        $booking->save();


        return redirect('/dashboard');
    }

    public function edit(Booking $booking)
    {
        if (auth()->user()->id == $booking->user_id) {
            return view('edit', compact('booking'));
        } else {
            return redirect('/dashboard');
        }
    }

    public function update(Request $request, Booking $booking)
    {
        if (isset($_POST['delete'])) {
            $booking->delete();
            return redirect('/dashboard');
        } else {
            $this->validate($request, [
                'start_time' => ['required', 'date', 'after:now', new BookingWeeklyLimit(auth()->user()->id), new BookingTimeLimit($request->end_time), new BookingOverlap(), new BookingAvailableScheudle],
                'end_time' => ['required', 'date', 'after:start_time', new BookingOverlap(), new BookingAvailableScheudle, new BookingWeeklyLimit]
            ]);
            $booking->start_time = $request->start_time;
            $booking->end_time = $request->end_time;
            $booking->save();
            return redirect('/dashboard');
        }
    }
}
