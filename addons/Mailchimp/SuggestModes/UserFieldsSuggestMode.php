<?php
namespace Statamic\Addons\Mailchimp\SuggestModes;

use Statamic\API\Fieldset;
use Statamic\Addons\Suggest\Modes\AbstractMode;

class UserFieldsSuggestMode extends AbstractMode
{
    public function suggestions()
    {
        return collect(Fieldset::get('user')->fields())->map(function ($field, $key) {
            return ['value' => $key, 'text' => $field['display'] ?? $key];
        })->values()->all();
    }
}