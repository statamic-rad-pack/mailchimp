<?php

namespace StatamicRadPack\Mailchimp\Listeners;

use Statamic\Events\UserRegistered;
use StatamicRadPack\Mailchimp\Subscriber;

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
