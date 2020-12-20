<?php

namespace Silentz\Mailchimp\Listeners;

use Silentz\Mailchimp\Subscriber;
use Statamic\Auth\User;
use Statamic\Events\UserRegistered;

class AddFromUser
{
    public function handle(UserRegistered $event)
    {
        if (! config('mailchimp.add_new_users')) {
            return;
        }

        tap(
            new Subscriber($this->getData($event->user), $this->getConfig()),
            fn (Subscriber $subscriber) => $subscriber->subscribe()
        );
    }

    private function getConfig()
    {
        return array_merge(config('mailchimp.users', []), ['form' => 'user']);
    }

    private function getData(User $user): array
    {
        return array_merge($user->data()->all(), ['email' => $user->email()]);
    }
}
