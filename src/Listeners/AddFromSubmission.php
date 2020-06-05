<?php

namespace Edalzell\Mailchimp\Listeners;

use DrewM\MailChimp\MailChimp;
use Edalzell\Mailchimp\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Statamic\Forms\Submission;
use Statamic\Support\Arr;

class AddFromSubmission
{
    private $config;

    public function __construct()
    {
        $this->config = config('mailchimp');
    }

    public function handle(Submission $submission)
    {
        if (! $formConfig = $this->formConfig($submission->form()->handle())) {
            return;
        }

        $subscriber = Subscriber::createFromSubmission($submission);

        // should we process this form and do we have permission to add them to mailchimp?
        // we'll also default to using email as the fieldname for the submission, if it's null subscribe() will attempt to use the primary_email_field set in the config for the formset.

        if ($this->hasPermission($subscriber, $formConfig)) {
            $subscriber->subscribe($formConfig);
        }
    }

    /**
     * Do we have permission to add them to mailchimp?
     *
     * @param $permissions array
     * @param $submitted_data array
     *
     * @return bool
     */
    private function hasPermission(Subscriber $subscriber, array $config)
    {
        if (!Arr::get($config, 'check_permission', false)) {
            return true;
        }

        $permission_field = Arr::get($config, 'permission_field');
        return $subscriber->hasPermission($permission_field);
    }


    /**
     * @param $formset_name string
     *
     * @return mixed
     *
     * Get the config params for the submitted form
     */
    private function formConfig($handle)
    {
        return collect($this->config['forms'])->firstWhere('blueprint', $handle);
    }
}
