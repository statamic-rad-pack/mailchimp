<?php

namespace Silentz\Mailchimp;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Newsletter\NewsletterFacade as Newsletter;
use Statamic\Support\Arr;

class Subscriber
{
    private Collection $data;
    private array $config;

    public function __construct(array|Collection $data, array $config)
    {
        $this->data = collect($data);
        $this->config = $config;
    }

    private function email(): string
    {
        return $this->get(Arr::get($this->config, 'primary_email_field', 'email'));
    }

    public function hasConsent(): bool
    {
        if (! Arr::get($this->config, 'check_consent', false)) {
            return true;
        }

        if (! $field = Arr::get($this->config, 'consent_field', 'consent')) {
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
        return collect($this->get(Arr::get($this->config, 'interests_field', 'interests'), []))
            ->flatMap(fn ($id) => [$id => true])
            ->all();
    }

    public function subscribe(): void
    {
        if (! $this->hasConsent() || empty($this->config)) {
            return;
        }

        $options = [
            'status' => Arr::get($this->config, 'disable_opt_in', false) ? 'subscribed' : 'pending',
            'tags' => Arr::wrap(Arr::get($this->config, 'tag')),
        ];

        if ($interests = $this->getInterests()) {
            $options = array_merge($options, ['interests' => $interests]);
        }

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

        if (! Newsletter::subscribeOrUpdate($this->email(), $mergeData, $this->config['form'], $options)) {
            Log::error(Newsletter::getLastError());
            Log::error(Newsletter::getApi()->getLastResponse());
        }
    }
}
