<?php

namespace StatamicRadPack\Mailchimp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function can_create_subscriber_from_submission()
    {
        $formConfig = [[
            'form' => 'contact_us',
            'check_consent' => true,
        ]];

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $this->assertInstanceOf(Subscriber::class, $subscriber);
    }

    #[Test]
    public function has_consent_by_default()
    {
        $formConfig = [
            'form' => 'post',
        ];

        $subscriber = new Subscriber($this->submission->data(), $formConfig);

        $consent = $subscriber->hasConsent();

        $this->assertTrue($consent);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function it_gets_config_from_form()
    {
        $settings = [
            'check_consent' => true,
            'consent_field' => 'consent',
            'disable_opt_in' => false,
            'interests_field' => 'interests',
            'marketing_permissions_field' => 'gdpr',
            'marketing_permissions_field_ids' => [],
            'merge_fields' => [
                [
                    'id' => 'KFC3e5jw',
                ],
            ],
            'primary_email_field' => 'email',
            'form' => 'contact_us',
        ];

        $this->form->merge([
            'mailchimp' => [
                'enabled' => true,
                'settings' => $settings,
            ],
        ])->save();

        $subscriber = Subscriber::fromSubmission($this->submission);

        $this->assertSame($settings, $subscriber->config());
        $this->assertSame($subscriber->email(), 'foo@bar.com');
    }
}
