<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/testify', function () {
    Storage::disk('s3')->put('test1.txt', 'test again');

    // If you didn't setup S3 as default disk in .env file
    $contents = Storage::disk('s3')->get('test1.txt');

    var_dump( $contents ); // This will show "test again text"
});

Route::get('/oldboard', function () {
    return view('oldboard');
})->middleware(['auth', 'verified'])->name('oldboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
