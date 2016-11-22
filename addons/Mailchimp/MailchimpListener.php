<?php

namespace Statamic\Addons\Mailchimp;

use Statamic\API\Helper;
use Statamic\Extend\Listener;
use DrewM\MailChimp\MailChimp;


class MailchimpListener extends Listener
{
    /**
     * The events to be listened for, and the methods to call.
     *
     * @var array
     */
    public $events = [
        'user.registered' => 'userRegistration',
        'Form.submission.created' => 'formSubmission'
    ];

    /**
     * @param $user \Statamic\Data\Users\User
     */
    public function userRegistration($user)
    {
        $this->subscribe($user->get('email'));
    }

    /**
     * @param $submission \Statamic\Forms\Submission
     *
     * @return null|array
     */
    public function formSubmission($submission)
    {
        $formsets = collect($this->getConfig('formsets'));
        
        // only do something if we're on the right formset
        if ($formsets->contains($submission->formset()->name()))
        {
            try
            {
                $this->subscribe($submission->get('email'));
            }
            catch (\Exception $e)
            {
                \Log::error($e->getMessage());
                return array('errors' => array($e->getMessage()));
            }
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

        $mailchimp = new MailChimp($this->getConfig('mailchimp_key'));

        $mailchimp->post('lists/' . $list . '/members', [
            'email_address' => $email,
            'status'        => 'subscribed',
        ]);

        if (!$mailchimp->success()) {
            throw new \Exception($mailchimp->getLastError());
        }
    }
}
