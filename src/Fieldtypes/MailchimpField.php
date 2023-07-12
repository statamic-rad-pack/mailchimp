<?php

namespace StatamicRadPack\Mailchimp\Fieldtypes;

use DrewM\MailChimp\MailChimp;
use Statamic\Fieldtypes\Relationship;

abstract class MailchimpField extends Relationship
{
    private ?MailChimp $mailchimp = null;

    public function __construct()
    {
        if (config('mailchimp.api_key')) {
            $this->mailchimp = app(MailChimp::class);
        }
    }

    protected function callApi(string $endpoint, array $data = []): ?array
    {
        return optional($this->mailchimp)->get($endpoint, $data);
    }
}
