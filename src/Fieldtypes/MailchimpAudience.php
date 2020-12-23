<?php

namespace Silentz\Mailchimp\Fieldtypes;

use Statamic\Support\Arr;

class MailchimpAudience extends MailchimpField
{
    public function getIndexItems($request)
    {
        return collect(Arr::get($this->callApi('lists'), 'lists', []))
            ->map(fn ($list) => ['id' => $list['id'], 'title' => $list['name']]);
    }

    protected function toItemArray($id)
    {
        if (! $id) {
            return [];
        }

        if (! $list = $this->callApi("lists/{$id}")) {
            return [];
        }

        return [
            'id' => $list['id'],
            'title' => $list['name'],
        ];
    }
}
