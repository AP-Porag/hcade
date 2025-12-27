<?php

use App\Http\Controllers\Admin\User\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
Route::get('/execute-command', function () {
//    return redirect()->route('login');
//    Artisan::call('storage:link');
    Artisan::call('migrate:fresh --seed');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize');
    dd('All commands executed successfully');
});
Route::get('/', function () {
    return redirect()->route('login');
//    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
