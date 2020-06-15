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
        $formConfig = [[
            'form' => 'post',
            'check_consent' => true
        ]];
        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $this->assertInstanceOf(Subscriber::class, $subscriber);
    }

    /** @test */
    public function has_consent_by_default()
    {
        $formConfig = [
            'form' => 'post',
        ];

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertTrue($consent);
    }

    /** @test */
    public function no_consent_when_no_consent_field()
    {
        $formConfig = [
            'form' => 'post',
            'check_consent' => true
        ];

        config(['mailchimp.forms' => $formConfig ]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertFalse($consent);
    }

    /** @test */
    public function no_consent_when_consent_field_is_false()
    {
        $formConfig = [
            'form' => 'post',
            'check_consent' => true
        ];

        $this->submission->set('consent_field', false);

        config(['mailchimp.forms' => $formConfig ]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertFalse($consent);
    }

    /** @test */
    public function consent_when_default_consent_field_is_true()
    {
        $formConfig = [
            'form' => 'post',
            'check_consent' => true
        ];

        $this->submission->set('consent', true);

        config(['mailchimp.forms' => $formConfig ]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertTrue($consent);
    }

    /** @test */
    public function consent_when_configured_consent_field_is_true()
    {
        $formConfig =
            [
                'blueprint' => 'post',
                'check_consent' => true,
                'consent_field' => 'the-consent'
            ];

        $this->submission->set('the-consent', true);

        config(['mailchimp.forms' => $formConfig ]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertTrue($consent);
    }
}
