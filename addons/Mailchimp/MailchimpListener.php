<?php

namespace Statamic\Addons\Mailchimp;

use Statamic\Extend\Listener;
use DrewM\MailChimp\MailChimp;


class MailchimpListener extends Listener
{
    /** @var  \DrewM\MailChimp\MailChimp */
    private $mailchimp;

    /**
     * The events to be listened for, and the methods to call.
     *
     * @var array
     */
    public $events = [
        'user.registered' => 'subscribe'
    ];

    public function init()
    {
        $this->mailchimp = new MailChimp($this->getConfig('key'));
    }

    /**
     * @param $user \Statamic\Contracts\Data\Users\User
     *
     * @throws \Exception
     */
    public function subscribe($user)
    {
        $list = $this->getConfig('list_id');
        $this->mailchimp->post('lists/' . $list . '/members', [
            'email_address' => $user->get('email'),
            'status'        => 'subscribed',
        ]);

        if (!$this->mailchimp->success()) {
            throw new \Exception($this->mailchimp->getLastError());
        }
    }
}
