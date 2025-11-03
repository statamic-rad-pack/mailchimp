<?php

namespace StatamicRadPack\Mailchimp;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\File;
use Statamic\Events\SubmissionCreated;
use Statamic\Events\UserRegistered;
use Statamic\Facades\Addon;
use Statamic\Facades\Form;
use Statamic\Facades\YAML;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;
use Statamic\Support\Arr;
use StatamicRadPack\Mailchimp\Listeners\AddFromSubmission;
use StatamicRadPack\Mailchimp\Listeners\AddFromUser;
use Stillat\Proteus\Support\Facades\ConfigWriter;

class ServiceProvider extends AddonServiceProvider
{
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

    public function bootAddon()
    {
        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', [
                '--tag' => 'mailchimp-config',
            ]);
        });

        $this->registerSettingsBlueprint(YAML::file(__DIR__.'/../resources/blueprints/config.yaml')->parse());

        $this->addFormConfigFields();

        $this->migrateToFormConfig();
        $this->migrateUserToSettings();

        $this->addFormsToNewsletterConfig();
    }

    public function register()
    {
        $this->app->singleton('newsletter', function () {
            $mailChimp = new Mailchimp(config('mailchimp.api_key'));
            $mailChimp->verify_ssl = config('mailchimp.use_ssl', true);

            $settings = Addon::get('statamic-rad-pack/mailchimp')->settings();

            $configuredLists = NewsletterListCollection::createFromSettings($settings);

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

        $settings = Addon::get('statamic-rad-pack/mailchimp')->settings();
        $lists['user'] = ['id' => $settings->get('users.audience_id')];

        $settings->set('lists', $lists);
    }

    private function addFormConfigFields()
    {
        Form::appendConfigFields('*', __('Mailchimp Integration'), [
            'mailchimp' => [
                'handle' => 'mailchimp',
                'type' => 'group',
                'display' => ' ',
                'fullscreen' => false,
                'border' => false,
                'full_width_setting' => true,
                'fields' => [
                    [
                        'handle' => 'enabled',
                        'field' => [
                            'type' => 'toggle',
                            'display' => __('Enabled'),
                            'width' => 100,
                            'full_width_setting' => true,
                        ],
                    ],
                    [
                        'handle' => 'settings',
                        'field' => [
                            'type' => 'group',
                            'display' => ' ',
                            'width' => 100,
                            'fullscreen' => false,
                            'full_width_setting' => true,
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

    private function migrateUserToSettings()
    {
        $config = UserConfig::load();

        if ($config->exists()) {
            $config = $config->config();

            $settings = Addon::get('statamic-rad-pack/mailchimp')->settings();
            $settings->set('add_new_users', $config['add_new_users']);
            $settings->set('users', [$config['users']]);
            $settings->save();

            File::delete(resource_path('mailchimp.yaml'));

            return;
        }
    }
}
