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
            $config = [];
            $config['check_permission'] = $this->getConfigBool('user_check_permission', false);
            $config['permission_field'] = $this->getConfig('user_permission_field');
            $config['merge_fields'] = $this->getConfig('user_merge_fields');

            if ($this->hasPermission($config, $user->data()))
            {
                $this->subscribe($user->email(), $user, $config);
            }
        }
    }

    /**
     * @param $submission \Statamic\Forms\Submission
     *
     * @throws
     */
    public function formSubmission($submission)
    {
        $formset_name = $submission->formset()->name();
        $form_config = $this->getFormConfig($formset_name);

        // should we process this form and do we have permission to add them to mailchimp?
        if ($this->shouldProcessForm($formset_name) &&
            $this->hasPermission($form_config, $submission->data())) {
            $this->subscribe($submission->get('email'), $submission, $form_config );
        }
    }

    /**
     * Only process the form if the submitted form is the formset in the config
     *
     * @param $formset_name string
     *
     * @return bool
     */
    private function shouldProcessForm($formset_name)
    {
        return collect($this->getConfig('forms'))->contains(function($ignore, $value) use ($formset_name)
        {
            return $formset_name == array_get($value, 'form');
        });
    }

    /**
     * Do we have permission to add them to mailchimp?
     *
     * @param $permissions array
     * @param $submitted_data array
     *
     * @return bool
     */
    private function hasPermission($permissions, $submitted_data)
    {
        $check_permission = array_get($permissions, 'check_permission', false);
        $permission_field = array_get($permissions, 'permission_field');
        $have_permission = request()->has($permission_field) ? filter_var(request($permission_field), FILTER_VALIDATE_BOOLEAN) : false;

        // if we don't need to check permission we can add OR
        // if we do need to check permission AND we have permission
        return (!$check_permission || ($check_permission && $have_permission));
    }

    /**
     * @param $email string
     * @param $merge_data \Statamic\Data\Users\User | \Statamic\Forms\Submission
     * @param $config array
     *
     * @throws \Exception
     */
    private function subscribe($email, $merge_data, $config)
    {
        $mailchimp = new MailChimp($this->getConfig('mailchimp_key'));

        $mailchimp_list_id = array_get($config, 'mailchimp_list_id');
        $disable_opt_in = array_get($config, 'disable_opt_in', false);

        $mailchimp->post('lists/' . $mailchimp_list_id . '/members', [
            'email_address' => $email,
            'status' => $disable_opt_in ? 'subscribed' : 'pending',
            'merge_fields' => collect(array_get($config, 'merge_fields', []))->map(function ($item, $key) use ($merge_data) {
                return [$item['tag'] => $merge_data->get($item['field_name'])];
            })->collapse()->all()
        ]);

        if (!$mailchimp->success())
        {
            \Log::error($mailchimp->getLastError());
        }
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
            return $formset_name == array_get($data, 'form');
        });
    }
}
