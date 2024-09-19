<?php

namespace StatamicRadPack\Mailchimp\Fieldtypes;

use Statamic\Support\Arr;

class MailchimpAudience extends MailchimpField
{
    public function getIndexItems($request)
    {
        return collect(Arr::get($this->callApi('lists', ['count' => 100]), 'lists', []))
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
        
        if (! $id = Arr::get($list, 'id')) {
            return [];
        }
    
        return [
            'id' => $id,
            'title' => Arr::get($list, 'name'),
        ];
    }
}
