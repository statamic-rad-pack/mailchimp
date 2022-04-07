<?php

namespace Silentz\Mailchimp;

use DrewM\MailChimp\MailChimp;
use Edalzell\Forma\Forma;
use Silentz\Mailchimp\Commands\GetGroups;
use Silentz\Mailchimp\Commands\GetInterests;
use Silentz\Mailchimp\Fieldtypes\MailchimpAudience;
use Silentz\Mailchimp\Fieldtypes\MailchimpMergeFields;
use Silentz\Mailchimp\Fieldtypes\MailchimpTag;
use Silentz\Mailchimp\Fieldtypes\UserFields;
use Silentz\Mailchimp\Http\Controllers\ConfigController;
use Silentz\Mailchimp\Listeners\AddFromSubmission;
use Silentz\Mailchimp\Listeners\AddFromUser;
use Spatie\Newsletter\NewsletterFacade;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\UserRegistered;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Support\Arr;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        GetGroups::class,
        GetInterests::class,
    ];

    protected $fieldtypes = [
        MailchimpAudience::class,
        MailchimpTag::class,
        // FormField::class,
        MailchimpMergeFields::class,
        UserFields::class,
    ];

    protected $listen = [
        UserRegistered::class => [AddFromUser::class],
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

        Forma::add('silentz/mailchimp', ConfigController::class);

        $this->app->booted(function () {
            $this->addFormsToNewsletterConfig();
        });
    }

    public function register()
    {
        $this->app->bind(MailChimp::class, fn ($app) => NewsletterFacade::getApi());
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

        $lists['user'] = ['id' => config('mailchimp.users.audience_id')];

        config(['newsletter.lists' => $lists]);
    }
}
