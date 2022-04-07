<?php

use Illuminate\Support\Facades\Route;
use Silentz\Mailchimp\Http\Controllers\GetMergeFieldsController;
use Silentz\Mailchimp\Http\Controllers\GetTagsController;
use Silentz\Mailchimp\Http\Controllers\GetUserFieldsController;

Route::name('mailchimp.')->prefix('mailchimp')->group(function () {
    Route::get('merge-fields/{list}', [GetMergeFieldsController::class, '__invoke'])->name('merge-fields');
    Route::get('tags/{list}', [GetTagsController::class, '__invoke'])->name('tags');
    Route::get('user-fields', [GetUserFieldsController::class, '__invoke'])->name('user-fields');
});
