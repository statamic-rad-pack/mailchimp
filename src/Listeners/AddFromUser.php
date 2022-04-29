<?php

namespace Silentz\Mailchimp\Listeners;

use Silentz\Mailchimp\Subscriber;
use Statamic\Events\UserRegistered;

class AddFromUser
{
    public function handle(UserRegistered $event)
    {
        if (! config('mailchimp.add_new_users')) {
            return;
        }

        Subscriber::fromUser($event->user)->subscribe();
    }
}
