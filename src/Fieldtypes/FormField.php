<?php

namespace Silentz\Mailchimp\Fieldtypes;

use Statamic\Facades\Form;
use Statamic\Fieldtypes\Relationship;

class FormField extends Relationship
{
    protected $component = 'form_field';

    public function getIndexItems($request)
    {
        return Form::find($request->input('form'))
            ->fields()
            ->map(fn ($field, $key) => ['id' => $key, 'title' => $field->display()])
            ->values()
            ->all();
    }

    protected function toItemArray($id)
    {
        if (! $id) {
            return [];
        }

        return [
            'id' => $id,
            'title' => $id,
        ];
    }
}
