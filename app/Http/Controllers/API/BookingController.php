<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Booking;
use App\Http\Resources\Booking as BookingResource;
use App\Rules\BookingWeeklyLimit;
use App\Rules\BookingOverlap;
use Carbon\Carbon;
use App\Rules\BookingAvailableScheudle;
use App\Rules\BookingTimeLimit;

class BookingController extends BaseController
{
    public function index()
    {

        $bookings = Booking::where('user_id', auth()->user()->id)
            ->get();
        return $this->sendResponse(BookingResource::collection($bookings), 'bookings fetched.');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'start_time' => ['required', 'date', 'after:now', new BookingOverlap(), new BookingAvailableScheudle, new BookingTimeLimit($request->end_time)],
            'end_time' => ['required','date','after:start_time', new BookingWeeklyLimit(), new BookingOverlap(), new BookingAvailableScheudle]
        ]);

        if ($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $booking = new Booking();
        $booking->start_time = $request->start_time;
        $booking->end_time = $request->end_time;
        $booking->user_id = auth()->user()->id;
        $beginningHour = Carbon::create($booking->start_time);
        $beginningHour->subUnitNoOverflow('hour', 25, 'day');
        $beginningHour->addUnitNoOverflow('hour', 9, 'day');
        $finishingHour =  Carbon::create($booking->end_time);
        $finishingHour->addUnitNoOverflow('hour', 25, 'day');
        $finishingHour->subUnitNoOverflow('hour', 1, 'day');
        //TODO implementar elseif para levantar los dos errores por diferente causa
        if(Booking::where('user_id','=',$booking->user_id)->where('start_time','>=', $beginningHour)->where('end_time', '<=', $finishingHour)->count() > 0){
            return $this->sendResponse(new BookingResource($booking), 'Has superado el límite de reservas diarias');
        }else{
            $booking->save();
        }

        return $this->sendResponse(new BookingResource($booking), 'Reserva creada');
    }

    public function show($id)
    {
        $booking = Booking::find($id);

        if (is_null($booking)) {
            return $this->sendError('La reserva no existe');
        }
        return $this->sendResponse(new BookingResource($booking), 'Reservas recuperadas');
    }

    public function update(Request $request, Booking $booking)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'start_time' => ['required', 'date', 'after:now', new BookingTimeLimit($request->end_time), new BookingAvailableScheudle],
            'end_time' => ['required', 'date', 'after:start_time', new BookingAvailableScheudle, new BookingWeeklyLimit]
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $booking->start_time = $input['start_time'];
        $booking->end_time = $input['end_time'];
        $beginningHour = Carbon::create($booking->start_time);
        $beginningHour->subUnitNoOverflow('hour', 25, 'day');
        $beginningHour->addUnitNoOverflow('hour', 9, 'day');
        $finishingHour =  Carbon::create($booking->end_time);
        $finishingHour->addUnitNoOverflow('hour', 25, 'day');
        $finishingHour->subUnitNoOverflow('hour', 1, 'day');

        //TODO implementar elseif para levantar los dos errores por diferente causa
        // && Booking::where('start_time', '<=', $booking->start_time)->where('end_time', '>=', $booking->end_time)->count() == 0
        if(Booking::where('user_id','=',$booking->user_id)->where('start_time','>=', $beginningHour)->where('end_time', '<=', $finishingHour)->count() <= 1 && Booking::where('start_time', '<=', $booking->start_time)->where('end_time', '>=', $booking->end_time)->count() == 0){
            $booking->save();
            return $this->sendResponse(new BookingResource($booking), 'Reserva actualizada con éxito');
        }
        return $this->sendResponse(new BookingResource($booking), 'Has superado el límite de reservas diarias o la pista ya esta reservada');



    }


  public function destroy($id)
    {
        $booking = Booking::find($id);
        if (is_null($booking)) {
            return $this->sendError('La reserva no existe, no se pudo eliminar ninguna reserva.');
        }
        else{
            $booking->delete();

            return $this->sendResponse([], 'Reserva eliminada.');
        }
    }



}