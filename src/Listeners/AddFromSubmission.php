<?php

namespace Edalzell\Mailchimp\Listeners;

use Edalzell\Mailchimp\Subscriber;
use Statamic\Forms\Submission;

class AddFromSubmission
{
    public function handle(Submission $submission)
    {
        $handle = $submission->form()->handle();
        $subscriber = tap(
            new Subscriber($submission->data(), $this->formConfig($handle)),
            function (Subscriber $subscriber) {
                $subscriber->subscribe();
            }
        );
    }

    private function formConfig(string $handle): array
    {
        return collect(config('mailchimp.forms'))
            ->firstWhere('blueprint', $handle) ?? [];
    }
}
