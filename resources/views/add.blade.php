<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Booking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">

                <form method="POST" action="/booking">

                    <div class="form-group">
                        <label for="start_time">Desde</label>
                        <input name="start_time"
                                  type="datetime-local"
                                  placeholder='Hora inicio'></input>
                        @if ($errors->has('start_time'))
                        <span class="text-danger">{{ $errors->first('start_time') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="end_time">Hasta</label>
                        <input name="end_time"
                                  type="datetime-local"
                                  placeholder='Hora final'></input>
                        @if ($errors->has('end_time'))
                        <span class="text-danger">{{ $errors->first('end_time') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Booking</button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
                <div class="flex">
                    <div class="flex-auto text-2xl mb-4">Horas ya ocupadas</div>
                </div>
                <table class="w-full text-md rounded mb-4">
                    <thead>
                    <tr class="border-b">
                        <th class="text-left p-3 px-5">Día/hora de comienzo</th>
                        <th class="text-left p-3 px-5">Día/hora de finalización</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bookings as $booking)
                    <tr class="border-b hover:bg-orange-100">
                        <td class="p-3 px-5">
                            {{$booking->start_time}}
                        </td>
                        <td class="p-3 px-5">
                            {{$booking->end_time}}
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
