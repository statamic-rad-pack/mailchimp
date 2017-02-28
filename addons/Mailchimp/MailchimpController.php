<?php

namespace Statamic\Addons\Mailchimp;

use Statamic\API\Form;
use Statamic\Extend\Controller;

class MailchimpController extends Controller
{
    public function getForms()
    {
        return collect(Form::all())->map(function ($form) {
            $fields = collect(array_keys(Form::get($form['name'])->formset()->data()['fields']));

            $fields = $fields->map(function ($field) {
                return [
                    'text' => ucfirst($field),
                    'value' => $field,
                ];
            });

            return [
                'text' => $form['title'],
                'value' => $form['name'],
                'fields' => $fields
            ];
        });
    }
}
