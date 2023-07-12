<?php

namespace StatamicRadPack\Mailchimp\Http\Controllers;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Arr;
use Statamic\Http\Controllers\Controller;

class GetTagsController extends Controller
{
    public function __invoke(string $list, MailChimp $mailchimp): array
    {
        return collect(Arr::get($mailchimp->get("lists/$list/segments", ['count' => 100]), 'segments', []))
            ->filter(fn ($segment) => $segment['type'] === 'static')
            ->map(fn ($segment) => ['id' => $segment['name'], 'label' => $segment['name']])
            ->all();
    }
}
