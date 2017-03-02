<?php

namespace Statamic\Addons\Mailchimp;

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
        if ($this->getConfigBool('add_new_users', false))
        {
            $permissions = [];
            $permissions['check_permission'] = $this->getConfigBool('user_check_permission', false);
            $permissions['permission_field'] = $this->getConfig('user_permission_field');

            if ($this->hasPermission($permissions, $user->data()))
            {
                $this->subscribe(
                    $user->email(),
                    $this->getConfig('user_mailchimp_list_id'),
                    $this->getConfigBool('user_disable_opt_in', false)
                );
            }
        }
    }

    /**
     * @param $submission \Statamic\Forms\Submission
     *
     * @return null|array
     */
    public function formSubmission($submission)
    {
        $formset_name = $submission->formset()->name();
        $form_config = $this->getFormConfig($formset_name);

        // should we process this form and do we have permission to add them to mailchimp?
        if ($this->shouldProcessForm($formset_name) &&
            $this->hasPermission($form_config['form_and_field'], $submission->data()))
        {
            $this->subscribe(
                $submission->get('email'),
                array_get($form_config, 'mailchimp_list_id'),
                array_get($form_config, 'disable_opt_in', false));
        }
    }

    /**
     * @param $formset_name string
     *
     * @return bool
     *
     * Only process the form if the submitted form is the formset in the config
     *
     */
    private function shouldProcessForm($formset_name)
    {
        return collect($this->getConfig('forms'))->contains(function($ignore, $value) use ($formset_name)
        {
            return $formset_name == array_get($value, 'form_and_field.form');
        });
    }

    /**
     * @param $permissions array
     * @param $submitted_data array
     *
     * @return bool
     *
     * Do we have permission to add them to mailchimp?
     */
    private function hasPermission($permissions, $submitted_data)
    {
        $check_permission = array_get($permissions, 'check_permission', false);
        $permission_field = array_get($permissions, 'permission_field');
        $have_permission = filter_var($submitted_data[$permission_field], FILTER_VALIDATE_BOOLEAN);

        // if we don't need to check permission we can add OR
        // if we do need to check permission AND we have permission
        return (!$check_permission || ($check_permission && $have_permission));
    }

    /**
     * @param $formset_name string
     *
     * @return mixed
     *
     * Get the config params for the submitted form
     */
    private function getFormConfig($formset_name)
    {
        return collect($this->getConfig('forms'))->first(function($ignored, $data) use ($formset_name) {
            return $formset_name == array_get($data, 'form_and_field.form');
        });
    }

    /**
     * @param $email string
     * @param $mailchimp_list_id string
     * @param $disable_opt_in bool
     *
     * @throws \Exception
     */
    private function subscribe($email, $mailchimp_list_id, $disable_opt_in = false)
    {
        $mailchimp = new MailChimp($this->getConfig('mailchimp_key'));

        $mailchimp->post('lists/' . $mailchimp_list_id . '/members', [
            'email_address' => $email,
            'status'        => $disable_opt_in ? 'subscribed' : 'pending',
        ]);

        if (!$mailchimp->success())
        {
            \Log::error($mailchimp->getLastError());
        }
    }
}
