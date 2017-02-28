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
        // only do something if we're on the right form and either we don't need to check permission
        // or we need to check permission and the permission field is truthy
        if ($this->allowed($submission))
        {
            try
            {
                $this->subscribe($submission);
            }
            catch (\Exception $e)
            {
                \Log::error($e->getMessage());
                return array('errors' => array($e->getMessage()));
            }
        }
    }

    /**
     * @param $submission \Statamic\Forms\Submission
     *
     * @return boolean
     */
    private function allowed($submission)
    {
        // only do something if we're on the right form and either we don't need to check permission
        // or we need to check permission and the permission field is truthy
        return collect($this->getConfig('forms'))->contains(function($ignore, $value) use ($submission)
        {
            $right_form = $submission->formset()->name() == array_get($value, 'form_and_field.form');
            $check_permission = array_get($value, 'form_and_field:check_permission');
            $permission_field = array_get($value, 'form_and_field:permission_field');
            $have_permission = filter_var($submission->get($permission_field), FILTER_VALIDATE_BOOLEAN);

            return $right_form && (!$check_permission || ($check_permission && $have_permission));
        });
    }

    private function getFormData($submission)
    {
        return collect($this->getConfig('forms'))->first(function($ignored, $data) use ($submission) {
            return $submission->formset()->name() == array_get($data, 'form_and_field.form');
        });
    }

    /**
     * @param $submission \Statamic\Forms\Submission
     *
     * @throws \Exception
     */
    private function subscribe($submission)
    {
        $mailchimp = new MailChimp($this->getConfig('mailchimp_key'));

        $form_data = $this->getFormData($submission);


        $mailchimp->post('lists/' . $form_data['mailchimp_list_id'] . '/members', [
            'email_address' => $submission->get('email'),
            'status'        => array_get($form_data, 'disable_opt_in', false) ? 'subscribed' : 'pending',
        ]);

        if (!$mailchimp->success()) {
            throw new \Exception($mailchimp->getLastError());
        }
    }
}
