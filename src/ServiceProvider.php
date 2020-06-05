<?php

namespace Edalzell\Mailchimp;

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
}
