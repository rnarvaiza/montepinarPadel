<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\EmailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth:sanctum', 'verified'])->group(function(){
    Route::get('/booking',[BookingsController::class, 'booking']);
    Route::get('/dashboard',[BookingsController::class, 'index'])->name('dashboard');
    Route::get('/booking',[BookingsController::class, 'add']);
    Route::post('/booking',[BookingsController::class, 'create']);

    Route::get('/booking/{booking}', [BookingsController::class, 'edit']);
    Route::post('/booking/{booking}', [BookingsController::class, 'update']);
});
Route::get('/sendemail', function () {
    try {
        $result = "Your email has been sent successfully";
        $data = array( 'name' => "Test padel montepinar" );
        Mail::send('emails.learning', $data, function ($message) {
            $from = 'info@rnarvaiza.me';
            $name = 'info';
            // cambiar el email y poner uno propio
            $to = 'rapazeo@gmail.com';
            $subject = "Learning Laravel test email";
            $message->from($from, $name);
            $message->to($to);
            $message->subject($subject);
        });
    } catch (Exception $e) {
        $result = $e->getMessage();
    }
    return $result;
});
Route::get('/email', [EmailController::class, 'create']);
Route::post('/email', [EmailController::class, 'sendEmail'])->name('send.email');
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
