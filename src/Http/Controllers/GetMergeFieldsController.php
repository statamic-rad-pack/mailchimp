<?php

namespace StatamicRadPack\Mailchimp\Http\Controllers;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Arr;
use Statamic\Http\Controllers\Controller;

class GetMergeFieldsController extends Controller
{
    public function __invoke(string $list, MailChimp $mailchimp): array
    {
        return collect(Arr::get($mailchimp->get("lists/$list/merge-fields"), 'merge_fields', []))
            ->map(fn ($mergeField) => ['id' => $mergeField['tag'], 'label' => $mergeField['name']])
            ->values()
            ->all();
    }
}
