<?php

use App\Helpers\K;
use App\Http\Middleware\Ready;
use App\Http\Middleware\Subscribed;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DSPController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepotController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RefuelController;
use App\Http\Controllers\WizardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StripeWebhookController;

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

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('cashier.webhook')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

Route::controller(Controller::class)->group(function () {
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacy-policy');
    Route::get('/terms-and-conditions', 'termsAndConditions')->name('terms-and-conditions');
});

Route::middleware(['auth'])->group(function () { # just signed in

    Route::controller(SubscriptionController::class)->group(function () {
        Route::get('/subscription', 'show')->name('subscription');
        Route::post('/subscription/success', 'success')->name('subscription.success');
        Route::get('/subscription/reject', 'reject')->name('subscription.reject');
        Route::post('/subscription/cancel', 'cancel')->name('subscription.cancel');
        Route::post('/subscription/resume', 'resume')->name('subscription.resume');
        Route::get('/coupon', 'coupon')->name('coupon');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/profile', 'show')->name('user.show');
        Route::patch('/profile', 'update')->name('user.update');
        Route::delete('/profile', 'destroy')->name('user.destroy');
        Route::post('/profile', 'options')->name('user.options');
    });

    Route::get('/keep-alive', function () {
        return response()->json(['alive' => true]);
    })->name('keep.alive');
});

Route::middleware(['auth', Subscribed::class])->group(function () {
    Route::controller(WizardController::class)->group(function () {
        Route::get('/wizard', 'show')->name('wizard');
    });

    Route::controller(DSPController::class)->group(function () {
        Route::post('/dsp/attach', 'attach')->name('dsp.attach');
        Route::post('/dsp/create', 'create')->name('dsp.create');
    });

    Route::post('/vehicle/create', [VehicleController::class, 'create'])->name('vehicle.create');
    Route::post('/depot/store', [DepotController::class, 'store'])->name('depot.store');
    Route::post('/rate/add', [RateController::class, 'add'])->name('rate.add');
});

Route::middleware(['auth', Subscribed::class, Ready::class])->group(function () {
    Route::get('/', [Controller::class, 'dashboard'])->name('dashboard');

    Route::controller(VehicleController::class)->group(function () {
        Route::get('/vehicle', 'show')->name('vehicle.show');
    });

    Route::controller(DepotController::class)->group(function () {
        Route::get('/depot/create', 'create')->name('depot.create');
    });

    Route::controller(DSPController::class)->group(function () {
        Route::get('/dsp', 'show')->name('dsp.show');
        Route::post('/dsp/{dsp}/edit', 'edit')->name('dsp.edit');
        Route::post('/dsp/{dsp}/detach', 'detach')->name('dsp.detach');
    });

    Route::controller(RouteController::class)->group(function () {
        Route::get('/routes', 'show')->name('route.show');
        Route::get('/routes/get', 'get')->name('route.get');
        Route::post('/route/add', 'add')->name('route.add');
        Route::post('/route/week', 'week')->name('route.week');
        Route::post('/route/bulk', 'bulk')->name('route.bulk');
        Route::post('/route/export', 'exportAll')->name('route.export');
        Route::post('/route/{route}/edit', 'edit')->name('route.edit');
        Route::post('/route/{route}/destroy', 'destroy')->name('route.destroy');
    });

    Route::controller(RateController::class)->group(function () {
        Route::get('/rate', 'show')->name('rate.show');
        Route::post('/rate/bulk', 'bulk')->name('rate.bulk');
        Route::post('/rate/export', 'exportAll')->name('rate.export');
        Route::post('/rate/{rate}/edit', 'edit')->name('rate.edit');
        Route::post('/rate/{rate}/destroy', 'destroy')->name('rate.destroy');
    });

    Route::controller(RefuelController::class)->group(function () {
        Route::get('/refuel/{vehicle}', 'show')->name('refuels');
        Route::post('/refuel/{vehicle}/add', 'add')->name('refuel.add');
        Route::post('/refuel/{vehicle}/bulk', 'bulk')->name('refuel.bulk');
        Route::post('/refuel/download', 'download')->name('refuel.download');
        Route::post('/refuel/{vehicle}/export', 'exportAll')->name('refuel.export');
        Route::post('/refuel/{refuel}/edit', 'edit')->name('refuel.edit');
        Route::post('/refuel/{refuel}/destroy', 'destroy')->name('refuel.destroy');
    });

    Route::controller(ExpenseController::class)->group(function () {
        Route::get('/expense', 'show')->name('expense.show');
        Route::post('/expense/add', 'add')->name('expense.add');
        Route::post('/expense/bulk', 'bulk')->name('expense.bulk');
        Route::post('/expense/download', 'download')->name('expense.download');
        Route::post('/expense/export', 'exportAll')->name('expense.export');
        Route::post('/expense/{expense}/edit', 'edit')->name('expense.edit');
        Route::post('/expense/{expense}/destroy', 'destroy')->name('expense.destroy');
    });

    Route::controller(TaxController::class)->group(function () {
        Route::get('/taxes/{year}', 'show')->name('tax.show');
        Route::post('/tax/{tax}/edit', 'edit')->name('tax.edit');
    });

    Route::controller(InfoController::class)->group(function () {
        Route::get('/map', 'show')->name('map.show');
        Route::put('/info', 'add')->name('info.add');
        Route::get('/info', 'info')->name('info.get');
        Route::patch('/info', 'update')->name('info.update');
        Route::patch('/info/loc', 'location')->name('info.location');
        Route::delete('/info', 'destroy')->name('info.destroy');
        Route::get('/google', 'keys')->name('google');
    });
});

require __DIR__ . '/auth.php';
