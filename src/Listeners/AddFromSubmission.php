<?php

namespace StatamicRadPack\Mailchimp\Listeners;

use Statamic\Events\SubmissionCreated;
use StatamicRadPack\Mailchimp\Subscriber;

class AddFromSubmission
{
    public function handle(SubmissionCreated $event)
    {
        Subscriber::fromSubmission($event->submission)->subscribe();
    }
}
