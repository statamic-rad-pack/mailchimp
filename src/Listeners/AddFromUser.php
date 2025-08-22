<?php

namespace StatamicRadPack\Mailchimp\Listeners;

use Statamic\Events\UserRegistered;
use Statamic\Facades\Addon;
use StatamicRadPack\Mailchimp\Subscriber;

class AddFromUser
{
    public function handle(UserRegistered $event)
    {
        if (! Addon::get('statamic-rad-pack/mailchimp')->settings()->get('add_new_users')) {
            return;
        }

        Subscriber::fromUser($event->user)->subscribe();
    }
}
