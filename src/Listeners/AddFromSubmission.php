<?php

namespace Silentz\Mailchimp\Listeners;

use Silentz\Mailchimp\Subscriber;
use Statamic\Events\SubmissionCreated;

class AddFromSubmission
{
    public function handle(SubmissionCreated $event)
    {
        Subscriber::fromSubmission($event->submission)->subscribe();
    }
}
