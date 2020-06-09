<?php

namespace Edalzell\Mailchimp\Listeners;

use Edalzell\Mailchimp\Subscriber;
use Statamic\Forms\Submission;

class AddFromSubmission
{
    public function handle(Submission $submission)
    {
        Subscriber::createFromSubmission($submission, $submission->form()->handle())->subscribe();
    }
}
