<?php

use App\Http\Controllers\ConnectController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/privacy', function () {
    return Inertia::render('PrivacyPolicy', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('privacy');

Route::get('/terms', function () {
    return Inertia::render('TermsOfService', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('terms');

Route::get('/cookies', function () {
    return Inertia::render('CookiePolicy', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('cookies');

Route::get('/acceptable-use', function () {
    return Inertia::render('AcceptableUse', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('acceptable-use');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    //Transactions
    Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions');

    //Setup pages
    Route::get('/connect/curve', [ConnectController::class, 'curve'])->name('connect.curve');
    Route::get('/connect', [ConnectController::class, 'connect'])->name('connect');

    //Sources
    Route::get('/connect/sources', [ConnectController::class, 'sources'])->name('connect.sources');
    Route::get('/connect/sources/{sourceType}/oauth', [ConnectController::class, 'connectSourceOauth'])->name('connect.sources.oauth');
    Route::get('/connect/sources/{sourceType}/callback', [ConnectController::class, 'connectSourceCallback'])->name('connect.sources.callback');
    Route::put('/connect/sources/{sourceId}/alter', [ConnectController::class, 'alterSource'])->name('connect.sources.alter');
    Route::delete('/connect/sources/{sourceId}', [ConnectController::class, 'deleteSource'])->name('connect.sources.delete');


    //Budgets/destinations
    Route::get('/connect/budgets', [ConnectController::class, 'budgets'])->name('connect.budgets');
    Route::get('/connect/budgets/{budgetType}/oauth', [ConnectController::class, 'connectBudgetOauth'])->name('connect.budgets.oauth');
    Route::get('/connect/budgets/{budgetType}/callback', [ConnectController::class, 'connectBudgetCallback'])->name('connect.budgets.callback');
    Route::delete('/connect/budgets/{budgetId}', [ConnectController::class, 'deleteBudget'])->name('connect.budgets.delete');

    //Billing
    Route::get('/plans', [PlansController::class, 'index'])->name('plans');
    Route::post('/plans', [PlansController::class, 'store'])->name('plans.create');
    Route::get('/billing-portal', function (Request $request) {
        return Inertia::location($request->user()->billingPortalUrl(route('plans')));
    })->name('billing-portal');

    //User
    Route::put('/user/uuid', [UserController::class, 'generateNewUuid'])->name('user.uuid.generate');
});

//Email webhook
Route::post('/webhook/email', [TransactionsController::class, 'inboundEmailHandler'])->name('webhook.email');
