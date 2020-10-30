<?php

namespace Edalzell\Mailchimp;

use Edalzell\Mailchimp\Fieldtypes\MailchimpAudience;
use Edalzell\Mailchimp\Listeners\AddFromSubmission;
use Statamic\CP\Navigation\Nav;
use Statamic\Events\SubmissionCreated;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        MailchimpAudience::class,
    ];

    protected $listen = [
        // 'user.registered' => [
        //     'Edalzell\Mailchimp\AddFromUser',
        // ],
        SubmissionCreated::class => [AddFromSubmission::class],
    ];

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    protected $scripts = [
        __DIR__.'/../dist/js/cp.js',
    ];

    public function boot()
    {
        parent::boot();

        $this->bootNav();

        $this->app->booted(fn () => $this->addFormsToNewsletterConfig());
    }

    private function bootNav()
    {
        NavAPI::extend(fn (Nav $nav) => $nav->content('Config')
                ->section('Mailchimp')
                ->route('mailchimp.config.edit')
                ->icon('settings-horizontal')
        );
    }

    private function addFormsToNewsletterConfig()
    {
        $lists = collect(config('mailchimp.forms'))
            ->flatMap(fn ($form) => [$form['form'] => ['id' => $form['audience_id']]])
            ->all();

        config(['newsletter.lists' => $lists]);
    }
}
