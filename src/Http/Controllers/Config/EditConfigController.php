<?php

namespace Silentz\Mailchimp\Http\Controllers\Config;

use Illuminate\Support\Arr;
use Silentz\Mailchimp\Concerns\HasBlueprint;
use Spatie\Newsletter\NewsletterFacade;
use Statamic\Http\Controllers\Controller;

class EditConfigController extends Controller
{
    use HasBlueprint;

    public function __invoke()
    {
        $blueprint = $this->getBlueprint();

        $fields = $blueprint
            ->fields()
            ->addValues($this->preProcess())
            ->preProcess();

        $mailchimp = NewsletterFacade::getApi();

        $lists = collect(Arr::get($mailchimp->get('lists'), 'lists'))
            ->map(fn ($list) => [
                'id' => $list['id'],
                'name' => $list['name'],
                'tags' => collect(Arr::get($mailchimp->get("lists/{$list['id']}/segments"), 'segments'))
                    ->filter(fn ($segment) => $segment['type'] === 'static')
                    ->map(fn ($segment) => Arr::only($segment, ['id', 'name']))
                    ->all(),
            ]);

        return view('mailchimp::cp.config.edit', [
            'blueprint' => $blueprint->toPublishArray(),
            'lists' => $lists,
            'meta' => $fields->meta(),
            'values' => $fields->values(),
        ]);
    }

    private function preProcess(): array
    {
        return config('mailchimp');
    }
}
