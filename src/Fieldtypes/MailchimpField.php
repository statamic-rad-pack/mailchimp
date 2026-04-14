<?php

namespace StatamicRadPack\Mailchimp\Fieldtypes;

use DrewM\MailChimp\MailChimp;
use Statamic\Fieldtypes\Relationship;

abstract class MailchimpField extends Relationship
{
    private ?MailChimp $mailchimp = null;

    protected function callApi(string $endpoint, array $data = []): ?array
    {
        if (! config('mailchimp.api_key')) {
            return [];
        }

        if (! $this->mailchimp) {
            $this->mailchimp = app(MailChimp::class);
        }

        return optional($this->mailchimp)->get($endpoint, $data);
    }
}
