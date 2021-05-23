<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Booking;
use App\Http\Resources\Booking as BookingResource;

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
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time'
        ]);

        if ($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $booking = new Booking();
        $booking->start_time = $request->start_time;
        $booking->end_time = $request->end_time;
        $booking->user_id = auth()->user()->id;
        $booking->save();

        return $this->sendResponse(new BookingResource($booking), 'Booking created.');
    }

    public function show($id)
    {
        $booking = Booking::find($id);

        if (is_null($booking)) {
            return $this->sendError('Booking does not exist.');
        }
        return $this->sendResponse(new BookingResource($booking), 'Booking fetched.');
    }

    public function update(Request $request, Booking $booking)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'start_time' => 'required',
           'end_time' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $booking->start_time = $input['start_time'];
        $booking->end_time = $input['end_time'];
        $booking->save();

        return $this->sendResponse(new BookingResource($booking), 'Booking updated.');
    }
/*
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return $this->sendResponse([], 'Booking deleted.');
    }

*/
    //Preguntar porqué ha cambiado paco el metodo destroy en clase.
    //https://dam.org.es/api-rest-con-laravel/

  public function destroy($id)
    {
        $booking = Booking::find($id);
        if (is_null($booking)) {
            return $this->sendError('Booking does not exist.', 'Booking not deleted.');
        }
        else{
            $booking->delete();

            return $this->sendResponse([], 'Booking deleted.');
        }
    }



}