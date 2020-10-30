<?php

use Edalzell\Mailchimp\Http\Controllers\Config\EditConfigController;
use Edalzell\Mailchimp\Http\Controllers\Config\UpdateConfigController;
use Illuminate\Support\Facades\Route;

Route::name('mailchimp.')->prefix('mailchimp')->group(function () {
    Route::name('config.')->prefix('config')->group(function () {
        Route::get('edit', [EditConfigController::class, '__invoke'])->name('edit');
        Route::post('update', [UpdateConfigController::class, '__invoke'])->name('update');
    });
});
