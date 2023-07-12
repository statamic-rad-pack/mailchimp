<?php

namespace StatamicRadPack\Mailchimp\Http\Controllers;

use Statamic\Facades\User;
use Statamic\Fields\Field;
use Statamic\Http\Controllers\Controller;

class GetUserFieldsController extends Controller
{
    public function __invoke(): array
    {
        return User::blueprint()->fields()->all()
            ->map(fn (Field $field, string $handle) => ['id' => $handle, 'label' => $field->display()])
            ->values()
            ->all();
    }
}
