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
        'user.registered' => 'userRegistration',
        'Form.submission.created' => 'formSubmission'
    ];

    public function init()
    {
        $this->mailchimp = new MailChimp($this->getConfig('mailchimp_key'));
    }

    /**
     * @param $user \Statamic\Data\Users\User
     */
    public function userRegistration($user)
    {
        $this->subscribe($user->get('email'));
    }

    /**
     * @param $submission \Statamic\Forms\Submission
     */
    public function formSubmission($submission)
    {
        // only do something if we're on the right formset
        if ($submission->formset()->name() === $this->getConfig('formset'))
        {
            $this->subscribe($submission->get('email'));
        }
    }

    /**
     * @param $email string
     *
     * @throws \Exception
     */
    private function subscribe($email)
    {
        $list = $this->getConfig('mailchimp_list_id');
        $this->mailchimp->post('lists/' . $list . '/members', [
            'email_address' => $email,
            'status'        => 'subscribed',
        ]);

        if (!$this->mailchimp->success()) {
            throw new \Exception($this->mailchimp->getLastError());
        }
    }
}
