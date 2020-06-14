<?php

namespace Edalzell\Mailchimp;

use Illuminate\Support\Facades\Log;
use Spatie\Newsletter\NewsletterFacade as Newsletter;
use Statamic\Support\Arr;

class Subscriber
{
    private array $data;
    private array $config;

    public function __construct(array $data, array $config)
    {
        $this->data = $data;
        $this->config = $config;
    }

    private function email(): string
    {
        return $this->get(Arr::get($this->config, 'primary_email_field', 'email'));
    }

    public function hasPermission(): bool
    {
        if (!Arr::get($this->config, 'check_permission', false)) {
            return true;
        }

        if (! $field = Arr::get($this->config, 'permission_field', 'permission')) {
            return false;
        }

        return $this->get($field, false);
    }

    private function get(string $field, $default = null)
    {
        return Arr::get($this->data, $field, $default);
    }

    public function subscribe(): void
    {
        if (!$this->hasPermission()) {
            return;
        }

        $options = [
            'status' => Arr::get($this->config, 'disable_opt_in', false) ? 'subscribed' : 'pending',
        ];

        $merge_fields = Arr::get($this->config, 'merge_fields', []);

        $mergeData = collect($merge_fields)->map(function ($item, $key) {
            // if there ain't nuthin' there, don't send nuthin'
            if (is_null($fieldData = $this->get($item['field_name']))) {
                return [];
            }

            // convert arrays to strings...Mailchimp don't like no arrays
            return [
                    $item['tag'] => is_array($fieldData) ? implode('|', $fieldData) : $fieldData,
                ];
        })->collapse()->all();

        if (!Newsletter::subscribe($this->email(), $mergeData, $this->config['form'], $options)) {
            Log::error(Newsletter::getLastError());
        }
    }
}
