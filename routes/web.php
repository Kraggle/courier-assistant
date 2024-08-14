<?php

use App\Helpers\K;
use App\Http\Middleware\Ready;
use App\Http\Middleware\Subscribed;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DSPController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\PostController;
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

Route::get('/get-status', function () {
    return response()->json(['status' => Auth::check()]);
})->name('get-status');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('cashier.webhook')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

Route::controller(Controller::class)->group(function () {
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacy-policy');
    Route::get('/terms-and-conditions', 'termsAndConditions')->name('terms-and-conditions');
});

Route::controller(PostController::class)->group(function () {
    Route::get('/news', 'news')->name('news');
    Route::get('/tips', 'tips')->name('tips');
    Route::get('/post/{slug}', 'post')->name('post');
    Route::post('/posts', 'get')->name('posts');
});

Route::middleware(['auth'])->group(function () { # not subscribed

    Route::controller(SubscriptionController::class)->group(function () {
        Route::get('/subscription', 'show')->name('subscription');
        Route::get('/subscription/success', 'success')->name('subscription.success');
        Route::post('/subscription', 'keys')->name('subscription.keys');
        Route::patch('/subscription', 'resume')->name('subscription.resume');
        Route::delete('/subscription', 'cancel')->name('subscription.cancel');
        Route::get('/coupon', 'coupon')->name('coupon');
        Route::get('/billing', 'billing')->name('billing');
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

    Route::controller(PostController::class)->group(function () {
        Route::get('/article/creator', 'creator')->name('post.creator');
        Route::get('/article/editor/{post}', 'editor')->name('post.editor');
        Route::put('/article/create', 'create')->name('post.create');
        Route::put('/article/{post}/update', 'update')->name('post.update');
        Route::put('/media/upload', 'upload')->name('media.upload');
        Route::post('/media/get', 'getMedia')->name('media.get');
        Route::delete('/media/delete', 'deleteMedia')->name('media.delete');
    });
});

Route::middleware(['auth', Subscribed::class])->group(function () { # subscribed
    Route::controller(WizardController::class)->group(function () {
        Route::get('/wizard', 'show')->name('wizard');
    });

    Route::controller(DSPController::class)->group(function () {
        Route::put('/dsp', 'create')->name('dsp.create');
        Route::patch('/dsp', 'attach')->name('dsp.attach');
    });

    Route::put('/vehicle', [VehicleController::class, 'create'])->name('vehicle.create');
    Route::put('/depot', [DepotController::class, 'store'])->name('depot.store');
    Route::put('/rate', [RateController::class, 'add'])->name('rate.add');
});

Route::middleware(['auth', Subscribed::class, Ready::class])->group(function () { # setup complete
    Route::get('/', [Controller::class, 'dashboard'])->name('dashboard');

    Route::controller(VehicleController::class)->group(function () {
        Route::get('/vehicle', 'show')->name('vehicle.show');
    });

    Route::controller(DepotController::class)->group(function () {
        Route::get('/depot', 'create')->name('depot.create');
    });

    Route::controller(DSPController::class)->group(function () {
        Route::get('/dsp', 'show')->name('dsp.show');
        Route::put('/dsp/{dsp}', 'edit')->name('dsp.edit');
        Route::delete('/dsp/{dsp}', 'detach')->name('dsp.detach');
    });

    Route::controller(RouteController::class)->group(function () {
        Route::put('/route', 'add')->name('route.add');
        Route::get('/routes', 'show')->name('route.show');
        Route::post('/routes', 'get')->name('route.get');
        Route::patch('/routes', 'week')->name('route.week');
        Route::patch('/routes/bulk', 'bulk')->name('route.bulk');
        Route::post('/routes/export', 'export')->name('route.export');
        Route::put('/route/{route}', 'edit')->name('route.edit');
        Route::delete('/route/{route}', 'destroy')->name('route.destroy');
    });

    Route::controller(RateController::class)->group(function () {
        Route::get('/rate', 'show')->name('rate.show');
        Route::patch('/rate', 'bulk')->name('rate.bulk');
        Route::post('/rate', 'export')->name('rate.export');
        Route::put('/rate/{rate}', 'edit')->name('rate.edit');
        Route::delete('/rate/{rate}', 'destroy')->name('rate.destroy');
    });

    Route::controller(RefuelController::class)->group(function () {
        Route::get('/refuels/{vehicle}', 'show')->name('refuels');
        Route::put('/refuels/{vehicle}', 'add')->name('refuel.add');
        Route::patch('/refuels/{vehicle}', 'bulk')->name('refuel.bulk');
        Route::post('/refuels/{vehicle}', 'export')->name('refuel.export');
        Route::post('/refuel', 'download')->name('refuel.download');
        Route::put('/refuel/{refuel}', 'edit')->name('refuel.edit');
        Route::delete('/refuel/{refuel}', 'destroy')->name('refuel.destroy');
    });

    Route::controller(ExpenseController::class)->group(function () {
        Route::get('/expense', 'show')->name('expense.show');
        Route::put('/expense', 'add')->name('expense.add');
        Route::patch('/expense', 'bulk')->name('expense.bulk');
        Route::post('/expense', 'download')->name('expense.download');
        Route::post('/expenses', 'export')->name('expense.export');
        Route::put('/expense/{expense}', 'edit')->name('expense.edit');
        Route::delete('/expense/{expense}', 'destroy')->name('expense.destroy');
    });

    Route::controller(TaxController::class)->group(function () {
        Route::get('/taxes/{year}', 'show')->name('tax.show');
        Route::put('/tax/{tax}', 'edit')->name('tax.edit');
    });

    Route::controller(InfoController::class)->group(function () {
        Route::get('/map', 'show')->name('map.show');
        Route::put('/info', 'add')->name('info.add');
        Route::get('/info', 'info')->name('info.get');
        Route::patch('/info', 'update')->name('info.update');
        Route::post('/info', 'location')->name('info.location');
        Route::delete('/info', 'destroy')->name('info.destroy');
        Route::get('/google', 'keys')->name('google');
    });
});

require __DIR__ . '/auth.php';
