<?php

namespace Edalzell\Mailchimp;

use DrewM\MailChimp\MailChimp;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        // 'user.registered' => [
        //     'Edalzell\Mailchimp\AddFromUser',
        // ],
        'Form.submission.created' => [
            'Edalzell\Mailchimp\Listeners\AddFromSubmission'
        ]
    ];

    public function register()
    {
        $this->app->bind(MailChimp::class, function () {
            return new MailChimp(config('mailchimp.key'));
        });
    }
}
