<?php

use App\Http\Controllers\CinemaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TicketController;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;


Route::get('/', [MovieController::class, 'home'])->name('home');
Route::get('/movies/all', [MovieController::class, 'homeAllMovie'])->name('home.movies.all');




Route::get('/signup', function () {
    return view('signup');
})->name('signup')->middleware('isGuest');

Route::post('/signup', [UserController::class, 'store'])->name('signup.store')->middleware('isGuest');

Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('isGuest');

Route::post('/login', [UserController::class, 'login'])->name('login.auth')->middleware('isGuest');

Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/schedules/{movie_id}', [MovieController::class, 'movieSchedule'])->name('schedules.detail');

Route::get('/cinemas/list', [CinemaController::class, 'listCinema'])->name('cinemas.list');
Route::get('/cinemas/{cinema_id}/schedules', [CinemaController::class, 'cinemaSchedules'])->name('cinemas.schedules');

Route::middleware('isUser')->group(function(){
    Route::get('/schedules/{scheduleId}/hours/{hourId}/show-seats', [TicketController::class, 'showSeats'])->name('schedules.show_seats');
    Route::prefix('/tickets')->name('tickets.')->group(function(){
        Route::get('/index', [TicketController::class, 'index'])->name('index');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticketId}/order', [TicketController::class, 'ticketOrder'])->name('order');
        Route::post('/{ticketId}/barcode', [TicketController::class, 'createBarcode'])->name('barcode');
        Route::get('/{ticketId}/payment', [TicketController::class, 'paymentPage'])->name('payment');
        Route::patch('/{ticketId}/payment/proof', [TicketController::class, 'proofPayment'])->name('payment.proof');
        Route::get('/{ticketId}/show', [TicketController::class, 'show'])->name('fajar');
        Route::get('/{ticketId}/show/bukti-pembayaran', [TicketController::class, 'exportPdf'])->name('export.pdf');
    });
});

// route yang ada dibawah ini Group() url awalnya adalah /admin dan name nya admin. karena di prefix/awalan url
Route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/tickets/chart', [TicketController::class, 'chartData'])->name('tickets.chart');
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    Route::prefix('/cinemas')->name('cinemas.')->group(function () {
        Route::get('/', [CinemaController::class, 'index'])->name('index');
        Route::get('/create', [CinemaController::class, 'create'])->name('create');
        Route::post('/store', [CinemaController::class, 'store'])->name('store');
        // {id} : parameter placeholder, digunakan untuk mengirimkan data ke controller
        // digunakan untu mencari spesifikasi data
        // {id} : id, karena bagian uniknya (pk) ada di id
        Route::get('/edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CinemaController::class, 'destroy'])->name('delete');
        Route::get('/export', [CinemaController::class, 'exportExcel'])->name('export');
        Route::get('/trash', [CinemaController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [CinemaController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [CinemaController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatable', [CinemaController::class, 'dataForDatatables'])->name('datatables');
    });
    Route::prefix('/users')->name('users.')->group(function() {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'storeStaff'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        Route::get('/export', [UserController::class, 'exportExcel'])->name('export');
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatable', [UserController::class, 'dataForDatatables'])->name('datatables');
    });
    Route::prefix('/movies')->name('movies.')->group(function () {
        Route::get('/chart', [MovieController::class, 'chartData'])->name('chart');
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MovieController::class, 'destroy'])->name('delete');
        Route::patch('/patch/{id}', [MovieController::class, 'patch'])->name('patch');
        Route::get('/export', [MovieController::class, 'exportExcel'])->name('export');
        Route::get('/trash', [MovieController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [MovieController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatable', [MovieController::class, 'dataForDatatables'])->name('datatables');
    });
});

Route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function(){
    Route::prefix('/promos')->name('promos.')->group(function(){
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::get('/create', [PromoController::class, 'create'])->name('create');
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PromoController::class, 'destroy'])->name('delete');
        Route::patch('/patch/{id}', [PromoController::class, 'patch'])->name('patch');
        Route::get('/export', [PromoController::class, 'exportExcel'])->name('export');
        Route::get('/trash', [PromoController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [PromoController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatable', [PromoController::class, 'dataForDatatables'])->name('datatables');
    });

    Route::prefix('/schedules')->name('schedules.')->group(function (){
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [ScheduleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ScheduleController::class, 'destroy'])->name('delete');
        Route::get('/trash', [ScheduleController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/export', [ScheduleController::class, 'exportExcel'])->name('export');
        Route::get('/datatable', [ScheduleController::class, 'dataForDatatables'])->name('datatables');
    });
});
