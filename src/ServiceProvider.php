<?php

namespace StatamicRadPack\Mailchimp;

use DrewM\MailChimp\MailChimp;
use Edalzell\Forma\Forma;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\UserRegistered;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;
use Statamic\Support\Arr;
use StatamicRadPack\Mailchimp\Commands\GetGroups;
use StatamicRadPack\Mailchimp\Commands\GetInterests;
use StatamicRadPack\Mailchimp\Commands\Permissions;
use StatamicRadPack\Mailchimp\Fieldtypes\FormFields;
use StatamicRadPack\Mailchimp\Fieldtypes\MailchimpAudience;
use StatamicRadPack\Mailchimp\Fieldtypes\MailchimpMergeFields;
use StatamicRadPack\Mailchimp\Fieldtypes\MailchimpTag;
use StatamicRadPack\Mailchimp\Fieldtypes\UserFields;
use StatamicRadPack\Mailchimp\Http\Controllers\ConfigController;
use StatamicRadPack\Mailchimp\Listeners\AddFromSubmission;
use StatamicRadPack\Mailchimp\Listeners\AddFromUser;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        GetGroups::class,
        GetInterests::class,
        Permissions::class,
    ];

    protected $fieldtypes = [
        FormFields::class,
        MailchimpAudience::class,
        MailchimpTag::class,
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

    protected $vite = [
        'input' => ['resources/js/cp.js'],
        'publicDirectory' => 'dist',
        'hotFile' => __DIR__.'/../dist/hot',
    ];

    public function boot()
    {
        parent::boot();

        Forma::add('statamic-rad-pack/mailchimp', ConfigController::class);

        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', [
                '--tag' => 'mailchimp-config',
            ]);
        });

        $this->app->booted(function () {
            $this->addFormsToNewsletterConfig();
        });
    }

    public function register()
    {
        $this->app->singleton('newsletter', function () {
            $mailChimp = new Mailchimp(config('mailchimp.api_key'));
            $mailChimp->verify_ssl = config('mailchimp.use_ssl', true);

            $configuredLists = NewsletterListCollection::createFromConfig(config('mailchimp'));

            return new NewsletterDriver($mailChimp, $configuredLists);
        });

        $this->app->bind(MailChimp::class, fn ($app) => Facades\Newsletter::getApi());
    }

    private function addFormsToNewsletterConfig()
    {
        $lists = collect(config('mailchimp.forms'))
            ->flatMap(function ($form) {
                if (! $handle = Arr::get($form, 'form')) {
                    return [];
                }

                return [
                    $handle => Arr::removeNullValues([
                        'id' => Arr::get($form, 'audience_id'),
                        'marketing_permissions' => collect(Arr::get($form, 'marketing_permissions_field_ids'))
                            ->filter()
                            ->flatMap(fn ($value) => [$value['field_name'] => $value['id']])
                            ->all(),
                    ]),
                ];
            })
            ->all();

        $lists['user'] = ['id' => config('mailchimp.users.audience_id')];

        config(['mailchimp.lists' => $lists]);
    }
}
