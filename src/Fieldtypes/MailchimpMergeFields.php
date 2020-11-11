<?php

namespace Silentz\Mailchimp\Fieldtypes;

use DrewM\MailChimp\MailChimp;
use Spatie\Newsletter\NewsletterFacade;
use Statamic\Fieldtypes\Relationship;
use Statamic\Support\Arr;

class MailchimpMergeFields extends Relationship
{
    protected $component = 'mailchimp_merge_fields';

    private MailChimp $mailchimp;

    public function __construct()
    {
        $this->mailchimp = NewsletterFacade::getApi();
    }

    public function getIndexItems($request)
    {
        return collect(Arr::get($this->mailchimp->get("lists/{$request->input('list')}/merge-fields"), 'merge_fields'))
            ->map(fn ($mergeField) => ['id' => $mergeField['tag'], 'title' => $mergeField['name']])
            ->all();
    }

    protected function toItemArray($id)
    {
    }
}
