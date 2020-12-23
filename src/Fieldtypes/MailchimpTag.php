<?php

namespace Silentz\Mailchimp\Fieldtypes;

use Statamic\Support\Arr;

class MailchimpTag extends MailchimpField
{
    protected $component = 'mailchimp_tag';

    public function getIndexItems($request)
    {
        return collect(Arr::get($this->callApi("lists/{$request->input('list')}/segments", ['count' => 100]), 'segments', []))
            ->filter(fn ($segment) => $segment['type'] === 'static')
            ->map(fn ($segment) => ['id' => $segment['name'], 'title' => $segment['name']])
            ->all();
    }

    protected function toItemArray($id)
    {
        return [];
        // if (! $id) {
        //     return [];
        // }

        // $list = $this->mailchimp->get("lists/{$id}");

        // return [
        //     'id' => $list['id'],
        //     'title' => $list['name'],
        // ];
    }
}
