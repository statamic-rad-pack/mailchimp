<?php

use Illuminate\Support\Facades\Route;
use StatamicRadPack\Mailchimp\Http\Controllers\GetFormFieldsController;
use StatamicRadPack\Mailchimp\Http\Controllers\GetMergeFieldsController;
use StatamicRadPack\Mailchimp\Http\Controllers\GetTagsController;
use StatamicRadPack\Mailchimp\Http\Controllers\GetUserFieldsController;

Route::name('mailchimp.')->prefix('mailchimp')->group(function () {
    Route::get('form-fields/{form}', [GetFormFieldsController::class, '__invoke'])->name('form-fields');
    Route::get('merge-fields/{list}', [GetMergeFieldsController::class, '__invoke'])->name('merge-fields');
    Route::get('tags/{list}', [GetTagsController::class, '__invoke'])->name('tags');
    Route::get('user-fields', [GetUserFieldsController::class, '__invoke'])->name('user-fields');
});
