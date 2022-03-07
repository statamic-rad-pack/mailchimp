<?php

namespace Silentz\Mailchimp\Fieldtypes;

use Statamic\Fields\Fieldtype;
use Statamic\Support\Arr;

class MailchimpMergeFields extends Fieldtype
{
    protected $component = 'mailchimp_merge_fields';

    // public function getIndexItems($request)
    // {
    //     return collect(Arr::get($this->callApi("lists/{$request->input('list')}/merge-fields"), 'merge_fields', []))
    //         ->map(fn ($mergeField) => ['id' => $mergeField['tag'], 'title' => $mergeField['name']])
    //         ->all();
    // }

    // protected function toItemArray($id)
    // {
    //     return [
    //         'id' => $id,
    //         'title' => $id,
    //     ];
    // }
}
