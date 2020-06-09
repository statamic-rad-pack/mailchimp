<?php

namespace Edalzell\Mailchimp\Tests\Unit;

use Edalzell\Mailchimp\Subscriber;
use Edalzell\Mailchimp\Tests\TestCase;
use Statamic\Facades\Form as FormAPI;
use Statamic\Fields\Blueprint;
use Statamic\Forms\Form as Form;
use Statamic\Forms\Submission;

class SubscriberTest extends TestCase
{
    private Form $form;
    private Submission $submission;

    public function setUp(): void
    {
        parent::setUp();

        FormAPI::all()->each->delete();

        $blueprint = (new Blueprint)->setHandle('post')->save();

        $this->form = FormAPI::make('contact_us')
            ->title('Contact Us')
            ->blueprint($blueprint)
            ->honeypot('winnie');

        $this->form->save();

        $this->submission = $this->form->createSubmission();
        $this->submission
            ->unguard()
            ->data(['foo'=>'bar']);
    }

    /** @test */
    public function can_create_subscriber_from_submission()
    {
        $subscriber = new Subscriber($this->submission->data(), 'post');

        $this->assertInstanceOf(Subscriber::class, $subscriber);
    }

    /** @test */
    public function has_permission_by_default()
    {
        $subscriber = Subscriber::createFromSubmission($this->submission, 'post');

        $permission = $subscriber->hasPermission();

        $this->assertTrue($permission);
    }

    /** @test */
    public function no_permission_when_no_permission_field()
    {
        $formConfig = [[
            'blueprint' => 'post',
            'check_permission' => true
        ]];

        config(['mailchimp.forms' => $formConfig ]);

        $subscriber = Subscriber::createFromSubmission($this->submission, 'post');


        $permission = $subscriber->hasPermission();

        $this->assertFalse($permission);
    }

    /** @test */
    public function no_permission_when_permission_field_is_false()
    {
        $formConfig = [[
            'blueprint' => 'post',
            'check_permission' => true
        ]];

        $this->submission->set('permission_field', false);

        config(['mailchimp.forms' => $formConfig ]);

        $subscriber = Subscriber::createFromSubmission($this->submission, 'post');

        $permission = $subscriber->hasPermission();

        $this->assertFalse($permission);
    }

    /** @test */
    public function permission_when_default_permission_field_is_true()
    {
        $formConfig = [
            [
                'blueprint' => 'post',
                'check_permission' => true
            ]
        ];

        $this->submission->set('permission', true);

        config(['mailchimp.forms' => $formConfig ]);

        $subscriber = Subscriber::createFromSubmission($this->submission, 'post');

        $permission = $subscriber->hasPermission();

        $this->assertTrue($permission);
    }

    /** @test */
    public function permission_when_configured_permission_field_is_true()
    {
        $formConfig = [
            [
                'blueprint' => 'post',
                'check_permission' => true,
                'permission_field' => 'the-permission'
            ]
        ];

        $this->submission->set('the-permission', true);

        config(['mailchimp.forms' => $formConfig ]);

        $subscriber = Subscriber::createFromSubmission($this->submission, 'post');

        $permission = $subscriber->hasPermission();

        $this->assertTrue($permission);
    }
}
