<?php

namespace Silentz\Mailchimp\Http\Controllers;

use Edalzell\Forma\ConfigController as BaseController;
use Illuminate\Support\Arr;

class ConfigController extends BaseController
{
    protected function postProcess(array $values): array
    {
        $userConfig = Arr::get($values, 'users');

        return array_merge(
            $values,
            ['users' => $userConfig[0]]
        );
    }

    protected function preProcess(string $handle): array
    {
        $config = config($handle);

        return array_merge(
            $config,
            ['users' => [Arr::get($config, 'users', [])]]
        );
    }
}
