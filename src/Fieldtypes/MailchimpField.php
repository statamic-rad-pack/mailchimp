<?php

namespace Silentz\Mailchimp\Fieldtypes;

use DrewM\MailChimp\MailChimp;
use Spatie\Newsletter\NewsletterFacade;
use Statamic\Fieldtypes\Relationship;

abstract class MailchimpField extends Relationship
{
    private ?MailChimp $mailchimp = null;

    public function __construct()
    {
        if (config('newsletter.apiKey')) {
            $this->mailchimp = NewsletterFacade::getApi();
        }
    }

    protected function callApi(string $endpoint, array $data = []): ?array
    {
        return optional($this->mailchimp)->get($endpoint, $data);
    }
}
