<?php

namespace Silentz\Mailchimp;

use Silentz\Mailchimp\Fieldtypes\MailchimpAudience;
use Silentz\Mailchimp\Listeners\AddFromSubmission;
use Statamic\CP\Navigation\Nav;
use Statamic\Events\SubmissionCreated;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Support\Arr;

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
            ->flatMap(function ($form) {
                if (! $handle = Arr::get($form, 'form')) {
                    return [];
                }

                return [$handle => ['id' => Arr::get($form, 'audience_id')]];
            })
            ->all();

        config(['newsletter.lists' => $lists]);
    }
}
