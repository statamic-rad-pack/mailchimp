<?php

namespace Edalzell\Mailchimp\Listeners;

use Edalzell\Mailchimp\Subscriber;
use Statamic\Events\SubmissionCreated;

class AddFromSubmission
{
    public function handle(SubmissionCreated $event)
    {
        $subscriber = new Subscriber($event->submission->data(), $this->formConfig($event->submission->form()->handle()));
        $subscriber->subscribe();
    }

    private function formConfig(string $handle): array
    {
        return collect(config('mailchimp.forms', []))->firstWhere('form', $handle) ?? [];
    }
}
