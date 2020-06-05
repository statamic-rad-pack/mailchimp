<?php

namespace Edalzell\Mailchimp;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\Log;
use Statamic\Auth\User;
use Statamic\Forms\Submission;
use Statamic\Support\Arr;

class Subscriber
{
    private $data;
    private $config;

    public static function createFromUser(User $user)
    {
        return new self($user->toShallowAugmentedArray());
    }

    public static function createFromSubmission(Submission $submission)
    {
        return new self($submission->data());
    }

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->config = config('mailchimp');
    }

    public function email()
    {
        return $this->get(Arr::get($this->config, 'primary_email_field', 'email'));
    }

    public function hasPermission($field)
    {
        return $this->get($field, false);
    }

    public function get($field, $default = null)
    {
        return Arr::get($this->data, $field, $default);
    }

    public function subscribe()
    {
        $data = [
            'email_address' => $this->email(),
            'status' => Arr::get($this->config, 'disable_opt_in', false) ? 'subscribed' : 'pending',
        ];

        if ($merge_fields = Arr::get($this->config, 'merge_fields')) {
            $data['merge_fields'] = collect($merge_fields)->map(function ($item, $key) {
                // if there ain't nuthin' there, don't send nuthin'
                if (is_null($fieldData = $this->get($item['field_name']))) {
                    return [];
                }

                // convert arrays to strings...Mailchimp don't like no arrays
                return [
                    $item['tag'] => is_array($fieldData) ? implode('|', $fieldData) : $fieldData,
                ];
            })->collapse()->all();
        }

        $mailchimp = new MailChimp($this->config['mailchimp_key']);
        $listId = Arr::get($this->config, 'listId');
        $hash = $mailchimp->subscriberHash($this->email());

        $mailchimp->put("lists/{$listId}/members/{$hash}", $data);

        if (!$mailchimp->success()) {
            Log::error($mailchimp->getLastError());
        }
    }
}
