<?php

use Illuminate\Support\Facades\Route;
use Silentz\Mailchimp\Http\Controllers\GetMergeFieldsController;

Route::name('mailchimp.')->prefix('mailchimp')->group(function () {
    Route::get('merge-fields/{list}', [GetMergeFieldsController::class, '__invoke'])->name('merge-fields');
});
