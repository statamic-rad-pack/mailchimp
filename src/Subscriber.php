<?php

namespace StatamicRadPack\Mailchimp;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Statamic\Auth\User;
use Statamic\Forms\Submission;
use Statamic\Support\Arr;
use StatamicRadPack\Mailchimp\Facades\Newsletter;

class Subscriber
{
    private Collection $data;

    private Collection $config;

    public static function fromSubmission(Submission $submission): ?self
    {
        if (! $form = $submission->form()) {
            return null;
        }

        if (! $config = $form->get('mailchimp', [])) {
            return null;
        }

        if (! $config['enabled'] ?? false) {
            return null;
        }

        if (! $config = Arr::get($config, 'settings', [])) {
            return null;
        }

        return new self($submission->data(), $config);
    }

    public static function fromUser(User $user): self
    {
        return new self(
            $user->data()->merge(['email' => $user->email()])->all(),
            array_merge(config('mailchimp.users', []), ['form' => 'user'])
        );
    }

    /**
     * @param  array|Collection  $data
     */
    public function __construct($data, ?array $config = null)
    {
        $this->data = collect($data);
        $this->config = collect($config);
    }

    public function config(): array
    {
        return $this->config->all();
    }

    public function email(): string
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
        if ($this->config->isEmpty()) {
            return;
        }

        if (! $this->hasConsent()) {
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
        })->collapse()
            ->all();

        // set gdpr marketing permissions
        $this->setMarketingPermissions();

        if (! Newsletter::subscribeOrUpdate($this->email(), $mergeData, $this->config->get('form'), $options)) {
            Log::error(Newsletter::getLastError());
            Log::error(Newsletter::getApi()->getLastResponse());
        }
    }

    public function tag(): ?string
    {
        if ($tagField = $this->config->get('tag_field')) {
            return $this->data->get($tagField);
        }

        return $this->config->get('tag');
    }

    private function setMarketingPermissions()
    {
        $gdprField = $this->config->get('marketing_permissions_field', 'gdpr');

        collect($this->data->get($gdprField))->each(function ($permission, $field) {
            $field = Arr::first($this->config->get('marketing_permissions_field_ids'), fn ($fieldId) => $fieldId['field_name'] == $field);

            Newsletter::setMarketingPermission(
                $this->email(),
                Arr::get($field, 'field_name'),
                true,
                $this->config->get('form')
            );
        });
    }
}
