<?php

namespace Edalzell\Mailchimp\Fieldtypes;

use DrewM\MailChimp\MailChimp;
use Spatie\Newsletter\NewsletterFacade;
use Statamic\Fieldtypes\Relationship;
use Statamic\Support\Arr;

class MailchimpAudience extends Relationship
{
    private MailChimp $mailchimp;

    public function __construct()
    {
        $this->mailchimp = NewsletterFacade::getApi();
    }

    public function getIndexItems($request)
    {
        $lists = collect(Arr::get($this->mailchimp->get('lists'), 'lists'));

        return $lists->map(fn ($list) => ['id' => $list['id'], 'title' => $list['name']]);
    }

    protected function toItemArray($id)
    {
        $list = $this->mailchimp->get("lists/{$id}");

        return [
            'id' => $list['id'],
            'title' => $list['name'],
        ];
    }
}
