<?php

namespace Edalzell\Mailchimp;

use Edalzell\Mailchimp\Listeners\AddFromSubmission;
use Statamic\Events\SubmissionCreated;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $publishAfterInstall = false;

    protected $listen = [
        // 'user.registered' => [
        //     'Edalzell\Mailchimp\AddFromUser',
        // ],
        SubmissionCreated::class => [AddFromSubmission::class],
    ];

    public function boot()
    {
        parent::boot();
        $this->app->booted(function () {
            $this->addFormsToNewsletterConfig();
        });

        $this->bootConfig();
    }

    /**
     * Setup the configuration for Charge.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/mailchimp.php',
            'mailchimp'
        );
    }

    private function bootConfig()
    {
        $this->publishes([
            __DIR__.'/../config/mailchimp.php' => $this->app->configPath('mailchimp.php'),
        ]);
    }

    private function addFormsToNewsletterConfig()
    {
        $lists = collect(config('mailchimp.forms'))->flatMap(function ($form) {
            return [$form['form'] => ['id'=> $form['audience_id']]];
        })->all();

        config(['newsletter.lists' => $lists]);
    }
}
