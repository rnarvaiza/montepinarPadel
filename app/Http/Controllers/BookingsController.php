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

    //TODO FALTA VALIDAR QUE UN MISMO USUARIO NO PUEDA RESERVAR MÁS DE UNA VEZ EN UN MISMO DÍA.
    //TODO Ordena las validaciones conforme a un criterio lógico, ej. primero las sencillas, luego las más complejas para evitar sobrecarga.

    public function create(Request $request)
    {

        $this->validate($request, [
            'start_time' => ['required', 'date', 'after:now', new BookingTimeLimit($request->end_time), new BookingOverlap(), new BookingAvailableScheudle],
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



        if(Booking::where('user_id','=',$booking->user_id)->where('start_time','>=', $beginningHour)->where('end_time', '<=', $finishingHour)->count() > 0){
            $this->message();
        }else{
            $booking->save();
        }

        return redirect('/dashboard');
    }

    public function message()
    {
        return 'Superado límite de reservas diarias.';
    }

    public function edit(Booking $booking)
    {
        if (auth()->user()->id == $booking->user_id) {
            return view('edit', compact('booking'));
        } else {
            return redirect('/dashboard');
        }
        $bookings = Booking::all();

        return view('add')->with('bookings', $bookings);
    }

    public function update(Request $request, Booking $booking)
    {
        if (isset($_POST['delete'])) {
            $booking->delete();
            return redirect('/dashboard');
        } else {
            $this->validate($request, [
                'start_time' => ['required', 'date', 'after:now', new BookingTimeLimit($request->end_time), new BookingAvailableScheudle],
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

            if(Booking::where('user_id','=',$booking->user_id)->where('start_time','>=', $beginningHour)->where('end_time', '<=', $finishingHour)->count() < 1 && Booking::where('start_time', '<=', $booking->start_time)->where('end_time', '>=', $booking->end_time)->count() == 0){
                $booking->save();
            }
            return redirect('/dashboard');
        }
    }
}
