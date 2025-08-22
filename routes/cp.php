<?php

use Illuminate\Support\Facades\Route;
use StatamicRadPack\Mailchimp\Http\Controllers;

Route::name('mailchimp.')->prefix('mailchimp')->group(function () {
    Route::get('form-fields/{form}', [Controllers\GetFormFieldsController::class, '__invoke'])->name('form-fields');
    Route::get('merge-fields/{list}', [Controllers\GetMergeFieldsController::class, '__invoke'])->name('merge-fields');
    Route::get('tags/{list}', [Controllers\GetTagsController::class, '__invoke'])->name('tags');
    Route::get('user-fields', [Controllers\GetUserFieldsController::class, '__invoke'])->name('user-fields');
});
