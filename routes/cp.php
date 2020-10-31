<?php

use Illuminate\Support\Facades\Route;
use Silentz\Mailchimp\Http\Controllers\Config\EditConfigController;
use Silentz\Mailchimp\Http\Controllers\Config\UpdateConfigController;

Route::name('mailchimp.')->prefix('mailchimp')->group(function () {
    Route::name('config.')->prefix('config')->group(function () {
        Route::get('edit', [EditConfigController::class, '__invoke'])->name('edit');
        Route::post('update', [UpdateConfigController::class, '__invoke'])->name('update');
    });
});
