<?php

namespace Silentz\Mailchimp;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Newsletter\NewsletterFacade as Newsletter;
use Statamic\Support\Arr;

class Subscriber
{
    private Collection $data;
    private Collection $config;

    /**
     * @param $data array|Collection
     */
    public function __construct($data, array $config)
    {
        $this->data = collect($data);
        $this->config = collect($config);
    }

    private function email(): string
    {
        return $this->get($this->config->get('primary_email_field', 'email'));
    }

    public function hasConsent(): bool
    {
        if (! $this->config->get('check_consent', false)) {
            return true;
        }

        if (! $field = $this->config->get('consent_field', 'consent')) {
            return false;
        }

        return filter_var(
            Arr::get(Arr::wrap($this->get($field, false)), 0, false),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    private function get(?string $field, $default = null)
    {
        return $this->data->get($field, $default);
    }

    private function getInterests(): array
    {
        return collect($this->get($this->config->get('interests_field', 'interests'), []))
            ->flatMap(fn ($id) => [$id => true])
            ->all();
    }

    public function subscribe(): void
    {
        if (! $this->hasConsent() || $this->config->isEmpty()) {
            return;
        }

        $options = [
            'status' => $this->config->get('disable_opt_in', false) ? 'subscribed' : 'pending',
            'tags' => Arr::wrap($this->tag()),
        ];

        if ($interests = $this->getInterests()) {
            $options = array_merge($options, ['interests' => $interests]);
        }

        $merge_fields = $this->config->get('merge_fields', []);

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

        if (! Newsletter::subscribeOrUpdate($this->email(), $mergeData, $this->config->get('form'), $options)) {
            Log::error(Newsletter::getLastError());
            Log::error(Newsletter::getApi()->getLastResponse());
        }
    }

    public function tag(): string
    {
        if ($tagField = $this->config->get('tag_field')) {
            return $this->data->get($tagField);
        }

        return $this->config->get('tag');
    }
}
