<?php

use App\Http\Controllers\Sharing\DemoActorController;
use App\Http\Controllers\Sharing\SharingItemController;
use App\Http\Controllers\Sharing\SharingRequestController;
use App\Http\Controllers\Sharing\SharingTransactionController;
use App\Http\Middleware\RequireSharingActor;
use App\Http\Middleware\SharingDemoOnly;
use Illuminate\Support\Facades\Route;

Route::prefix('sharing')->name('sharing.')->middleware(SharingDemoOnly::class)->group(function (): void {
    Route::get('/demo', [DemoActorController::class, 'index'])->name('demo');
    Route::post('/demo/actor', [DemoActorController::class, 'switch'])->name('demo.switch');
    Route::post('/demo/end', [DemoActorController::class, 'end'])->name('demo.end');

    Route::middleware(RequireSharingActor::class)->group(function (): void {
        Route::redirect('/', '/sharing/transactions')->name('index');
        Route::get('/items', [SharingItemController::class, 'index'])->name('items.index');
        Route::get('/items/{item}', [SharingItemController::class, 'show'])->whereNumber('item')->name('items.show');
        Route::get('/items/{item}/request', [SharingRequestController::class, 'create'])->whereNumber('item')->name('requests.create');
        Route::post('/items/{item}/requests', [SharingRequestController::class, 'store'])->whereNumber('item')->name('requests.store');
        Route::get('/incoming', [SharingRequestController::class, 'incoming'])->name('requests.incoming');
        Route::get('/requests/{sharingRequest}', [SharingRequestController::class, 'show'])->whereNumber('sharingRequest')->name('requests.show');
        Route::post('/requests/{sharingRequest}/accept', [SharingRequestController::class, 'accept'])->whereNumber('sharingRequest')->name('requests.accept');
        Route::post('/requests/{sharingRequest}/reject', [SharingRequestController::class, 'reject'])->whereNumber('sharingRequest')->name('requests.reject');
        Route::get('/transactions', [SharingTransactionController::class, 'index'])->name('transactions.index');
    });
});
