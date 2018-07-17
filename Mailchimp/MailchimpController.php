<?php

namespace Statamic\Addons\Mailchimp;

use Statamic\API\Form;
use Statamic\Extend\Controller;

class MailchimpController extends Controller
{
    public function getFields()
    {
        $fields = [];

        if ($formName = request()->query('form')) {
            $fields = collect(Form::get($formName)->fields())->map(function ($field, $name) {
                return [
                    'text' => isset($field['display']) ? $field['display'] : ucwords($name),
                    'value' => $name
                ];
            })->values()->all();
        }

        return $fields;
    }

    public function getUpdateMember()
    {
        $config = [];
        $config['merge_fields'] = $this->getConfig('user_merge_fields');

        $merge_fields = array_get($config, 'merge_fields', []);


        $foo = collect($merge_fields)->map(function ($item, $key) {
            return [$item['tag'] => 'Erin'];
        })->collapse()->all();

        dd($foo);

    }
}
