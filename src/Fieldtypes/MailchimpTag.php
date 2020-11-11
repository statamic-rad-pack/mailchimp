<?php

namespace Silentz\Mailchimp\Fieldtypes;

use DrewM\MailChimp\MailChimp;
use Spatie\Newsletter\NewsletterFacade;
use Statamic\Fieldtypes\Relationship;
use Statamic\Support\Arr;

class MailchimpTag extends Relationship
{
    protected $component = 'mailchimp_tag';

    private MailChimp $mailchimp;

    public function __construct()
    {
        $this->mailchimp = NewsletterFacade::getApi();
    }

    public function getIndexItems($request)
    {
        return collect(Arr::get($this->mailchimp->get("lists/{$request->input('list')}/segments"), 'segments'))
            ->filter(fn ($segment) => $segment['type'] === 'static')
            ->map(fn ($segment) => ['id' => $segment['name'], 'title' => $segment['name']])
            ->all();
    }

    protected function toItemArray($id)
    {
        return [];
        // if (! $id) {
        //     return [];
        // }

        // $list = $this->mailchimp->get("lists/{$id}");

        // return [
        //     'id' => $list['id'],
        //     'title' => $list['name'],
        // ];
    }
}
