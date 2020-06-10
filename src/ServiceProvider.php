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

    public function boot()
    {
        $this->app->booted(function () {
            $this->addFormsToNewsletterConfig();
        });
    }

    private function addFormsToNewsletterConfig()
    {
        $lists = collect(config('mailchimp.forms'))->flatMap(function ($form) {
            return [$form['blueprint'] => ['id'=> 'mailchimp_list_id']];
        })->all();

        config(['newsletter.lists' => $lists]);
    }
}
