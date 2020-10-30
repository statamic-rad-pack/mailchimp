<?php

namespace Edalzell\Mailchimp\Http\Controllers\Config;

use Edalzell\Mailchimp\Concerns\HasBlueprint;
use Illuminate\Support\Arr;
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
            ->map(function ($list) use ($mailchimp) {
                $tags = collect(Arr::get($mailchimp->get("lists/{$list['id']}/segments"), 'segments'))
                    ->filter(function ($segment) {
                        return $segment['type'] === 'static';
                    })
                    ->map(function ($segment) {
                        return Arr::only($segment, ['id', 'name']);
                    })
                    ->all();

                return [
                    'id' => $list['id'],
                    'name' => $list['name'],
                    'tags' => $tags,
                ];
            });

        return view('mailchimp::cp.config.edit', [
                'blueprint' => $blueprint->toPublishArray(),
                'lists' => $lists,
                'meta' => $fields->meta(),
                'values' => $fields->values(),
            ]);
    }

    private function preProcess(): array
    {
        $config = config('mailchimp');

        return $config;
        // return Arr::set(
        //     $config,
        //     'forms',
        //     collect(Arr::get($config, 'forms'))
        //         ->map(fn ($data, $key) => Arr::set($data, 'handle', $key))
        //         ->values()
        //         ->all()
        // );
    }
}
