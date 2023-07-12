<?php

namespace StatamicRadPack\Mailchimp\Tests\Unit;

use Statamic\Facades\Form as FormAPI;
use Statamic\Forms\Form;
use Statamic\Forms\Submission;
use StatamicRadPack\Mailchimp\Subscriber;
use StatamicRadPack\Mailchimp\Tests\TestCase;

class SubscriberFromSubmissionTest extends TestCase
{
    private Form $form;

    private Submission $submission;

    public function setUp(): void
    {
        parent::setUp();

        $this->form = FormAPI::make('contact_us')
            ->title('Contact Us')
            ->honeypot('winnie');

        $this->form->save();

        $this->submission = $this->form->makeSubmission();
        $this->submission
            ->data([
                'email' => 'foo@bar.com',
                'first_name' => 'Foo',
                'last_name' => 'Bar',
            ]);
    }

    /** @test */
    public function can_create_subscriber_from_submission()
    {
        $formConfig = [[
            'form' => 'contact_us',
            'check_consent' => true,
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
            'check_consent' => true,
        ];

        config(['mailchimp.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertFalse($consent);
    }

    /** @test */
    public function no_consent_when_consent_field_is_false()
    {
        $formConfig = [
            'form' => 'post',
            'check_consent' => true,
        ];

        $this->submission->set('consent_field', false);

        config(['mailchimp.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertFalse($consent);
    }

    /** @test */
    public function consent_when_default_consent_field_is_true()
    {
        $formConfig = [
            'form' => 'post',
            'check_consent' => true,
        ];

        $this->submission->set('consent', true);

        config(['mailchimp.forms' => $formConfig]);

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
                'consent_field' => 'the-consent',
            ];

        $this->submission->set('the-consent', true);

        config(['mailchimp.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertTrue($consent);
    }

    /** @test */
    public function uses_tag_config_when_present()
    {
        $formConfig =
                [
                    'blueprint' => 'post',
                    'tag' => 'foo',
                ];

        $this->submission->set('tag', 'foo');

        config(['mailchimp.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $this->assertEquals('foo', $subscriber->tag());
    }

    /** @test */
    public function uses_tag_field_when_present()
    {
        $formConfig =
            [
                'blueprint' => 'post',
                'tag' => 'bar',
                'tag_field' => 'tag',
            ];

        $this->submission->set('tag', 'foo');

        config(['mailchimp.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $this->assertEquals('foo', $subscriber->tag());
    }

    /** @test */
    public function skips_tag_field_when_not_present()
    {
        $formConfig =
            [
                'blueprint' => 'post',
            ];

        $this->submission->set('tag', 'foo');

        config(['mailchimp.forms' => $formConfig]);

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $this->assertNull($subscriber->tag());
    }
}
