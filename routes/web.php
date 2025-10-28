<?php

use App\Livewire\Sales\POS;
use Laravel\Fortify\Features;
use App\Livewire\Sales\ShiftKasir;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Livewire\MasterData\CabangToko;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use App\Livewire\MasterData\ProductList;
use App\Livewire\MasterData\UserManagement;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard',Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('pos',POS::class)->name('pos');
    Route::get('list-toko',CabangToko::class)->name('list-toko');
    Route::get('users',UserManagement::class)->name('users');
    Route::get('products', ProductList::class)->name('products.index');
    Route::get('shift-kasir', ShiftKasir::class)->name('shift-kasir');


});



require __DIR__ . '/auth.php';


/* --- Start Route Support --- */
Route::get('/optimize', function () {
    Artisan::call('optimize:clear');
    return redirect('/');
});
Route::get('/clear', function () {
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    return redirect('/');
});
Route::get('/cache', function () {
    Artisan::call('view:cache');
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    return redirect('/');
});
Route::get('/dest', function (Request $req) {
    $req->session()->invalidate();
    $req->session()->regenerateToken();

    return redirect('/');
})->name('dest');
Route::get('/laravel-version', function () {
    return response()->json(['version' => app()->version()]);
});
