<?php

namespace StatamicRadPack\Mailchimp;

use DrewM\MailChimp\MailChimp;
use Edalzell\Forma\Forma;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\UserRegistered;
use Statamic\Facades\Form;
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
use Stillat\Proteus\Support\Facades\ConfigWriter;

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

        $this->addFormConfigFields();

        $this->app->booted(function () {
            $this->addFormsToNewsletterConfig();
        });

        $this->migrateToFormConfig();
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
        $lists = Form::all()
            ->flatMap(function ($form) {
                $data = $form->get('mailchimp', []);

                if (! $enabled = Arr::get($data, 'enabled')) {
                    return [];
                }

                if (! $data = Arr::get($data, 'settings', [])) {
                    return [];
                }

                return [
                    $form->handle() => Arr::removeNullValues([
                        'id' => Arr::get($data, 'audience_id'),
                        'marketing_permissions' => collect(Arr::get($data, 'marketing_permissions_field_ids'))
                            ->filter()
                            ->flatMap(fn ($value) => [$value['field_name'] => $value['id']])
                            ->all(),
                    ]),
                ];
            })
            ->filter()
            ->all();

        $lists['user'] = ['id' => config('mailchimp.users.audience_id')];

        config(['mailchimp.lists' => $lists]);
    }

    private function addFormConfigFields()
    {

        Form::appendConfigFields('*', __('Mailchimp'), [
            'mailchimp' => [
                'handle' => 'mailchimp',
                'type' => 'group',
                'display' => ' ',
                'fullscreen' => false,
                'border' => false,
                'fields' => [
                    [
                        'handle' => 'enabled',
                        'field' => [
                            'type' => 'toggle',
                            'display' => __('Enabled'),
                            'width' => 100,
                        ],
                    ],

                    [
                        'handle' => 'settings',
                        'field' => [
                            'type' => 'group',
                            'display' => ' ',
                            'width' => 100,
                            'fullscreen' => false,
                            'show_when' => ['enabled' => true],
                            'fields' => [

                                [
                                    'handle' => 'primary_email_field',
                                    'field' => [
                                        'type' => 'form_fields',
                                        'max_items' => 1,
                                        'default' => 'email',
                                        'display' => __('Email Field'),
                                        'width' => 33,
                                    ],
                                ],

                                [
                                    'handle' => 'interests_field',
                                    'field' => [
                                        'type' => 'form_fields',
                                        'max_items' => 1,
                                        'display' => __('Interests Field'),
                                        'width' => 33,
                                    ],
                                ],

                                [
                                    'handle' => 'audience_id',
                                    'field' => [
                                        'type' => 'mailchimp_audience',
                                        'mode' => 'select',
                                        'max_items' => 1,
                                        'display' => __('Audience ID'),
                                        'width' => 33,
                                    ],
                                ],

                                [
                                    'handle' => 'tag',
                                    'field' => [
                                        'type' => 'mailchimp_tag',
                                        'max_items' => 1,
                                        'display' => __('Tag'),
                                        'width' => 33,
                                    ],
                                ],

                                [
                                    'handle' => 'tag_field',
                                    'field' => [
                                        'type' => 'form_fields',
                                        'max_items' => 1,
                                        'display' => __('Tag Field'),
                                        'width' => 33,
                                    ],
                                ],

                                [
                                    'handle' => 'disable_opt_in',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => __('Disable Opt In?'),
                                        'width' => 33,
                                        'default' => false,
                                    ],
                                ],

                                [
                                    'handle' => 'check_consent',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => __('Check Consent?'),
                                        'width' => 33,
                                        'default' => false,
                                    ],
                                ],

                                [
                                    'handle' => 'consent_field',
                                    'field' => [
                                        'type' => 'form_fields',
                                        'max_items' => 1,
                                        'display' => __('Consent Field'),
                                        'width' => 33,
                                        'if' => ['check_consent' => true],
                                    ],
                                ],

                                [
                                    'handle' => 'marketing_permissions_field',
                                    'field' => [
                                        'type' => 'form_fields',
                                        'max_items' => 1,
                                        'display' => __('Marketing Permissions Field'),
                                    ],
                                ],

                                [
                                    'handle' => 'marketing_permissions_field_ids',
                                    'field' => [
                                        'type' => 'grid',
                                        'mode' => 'table',
                                        'reorderable' => true,
                                        'listable' => 'hidden',
                                        'display' => __('Marketing Permissions'),
                                        'width' => 100,
                                        'add_row' => __('Add Permission Field'),
                                        'fields' => [

                                            [
                                                'handle' => 'field_name',
                                                'field' => [
                                                    'type' => 'text',
                                                    'display' => __('Form Field'),
                                                    'width' => 33,
                                                ],
                                            ],

                                            [
                                                'handle' => 'id',
                                                'field' => [
                                                    'type' => 'text',
                                                    'display' => __('ID'),
                                                    'width' => 33,
                                                ],
                                            ],

                                        ],
                                    ],
                                ],

                                [
                                    'handle' => 'merge_fields',
                                    'field' => [
                                        'type' => 'grid',
                                        'mode' => 'table',
                                        'reorderable' => true,
                                        'listable' => 'hidden',
                                        'display' => __('Merge Fields'),
                                        'width' => 100,
                                        'add_row' => __('Add Merge Field'),
                                        'fields' => [

                                            [
                                                'handle' => 'field_name',
                                                'field' => [
                                                    'type' => 'form_fields',
                                                    'display' => __('Form Field'),
                                                    'width' => 33,
                                                ],
                                            ],

                                            [
                                                'handle' => 'tag',
                                                'field' => [
                                                    'type' => 'mailchimp_merge_fields',
                                                    'display' => __('Merge Field'),
                                                    'max_items' => 1,
                                                    'width' => 33,
                                                ],
                                            ],

                                        ],
                                    ],
                                ],

                            ],

                        ],

                    ],

                ],
            ],
        ]);
    }

    private function migrateToFormConfig()
    {
        if (! $forms = config('mailchimp.forms')) {
            return;
        }

        foreach ($forms as $config) {
            (new Migrators\ConfigToFormData)->handle($config);
        }

        ConfigWriter::edit('mailchimp')->remove('forms')->save();
    }
}
