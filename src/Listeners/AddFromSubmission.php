<?php

namespace Edalzell\Mailchimp\Listeners;

use Edalzell\Mailchimp\Subscriber;
use Statamic\Forms\Submission;

class AddFromSubmission
{
    public function handle(Submission $submission)
    {
        $subscriber = new Subscriber($submission->data(), $this->formConfig($submission->form()->handle()));
        $subscriber->subscribe();
    }

    private function formConfig(string $handle): array
    {
        return collect(config('mailchimp.forms'))
            ->firstWhere('form', $handle) ?? [];
    }
}
