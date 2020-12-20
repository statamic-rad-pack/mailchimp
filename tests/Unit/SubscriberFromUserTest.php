<?php

namespace Silentz\Mailchimp\Tests\Unit;

use Silentz\Mailchimp\Subscriber;
use Silentz\Mailchimp\Tests\TestCase;
use Statamic\Facades\User;

class SubscriberFromUserTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::make()
            ->email('foo@bar.com')
            ->password('password');
    }

    // /** @test */
    // public function can_create_subscriber_from_user()
    // {
    //     $formConfig = [[
    //         'form' => 'post',
    //         'check_consent' => true,
    //     ]];
    //     $subscriber = new Subscriber($this->user->data(), $formConfig);

    //     $this->assertInstanceOf(Subscriber::class, $subscriber);
    // }

    // /** @test */
    // public function has_consent_by_default()
    // {
    //     $formConfig = [
    //         'form' => 'post',
    //     ];

    //     $subscriber = new Subscriber($this->submission->data(), $formConfig);

    //     $consent = $subscriber->hasConsent();

    //     $this->assertTrue($consent);
    // }

    // /** @test */
    // public function no_consent_when_no_consent_field()
    // {
    //     $formConfig = [
    //         'form' => 'post',
    //         'check_consent' => true,
    //     ];

    //     config(['mailchimp.forms' => $formConfig]);

    //     $subscriber = new Subscriber($this->submission->data(), $formConfig);

    //     $consent = $subscriber->hasConsent();

    //     $this->assertFalse($consent);
    // }

    // /** @test */
    // public function no_consent_when_consent_field_is_false()
    // {
    //     $formConfig = [
    //         'form' => 'post',
    //         'check_consent' => true,
    //     ];

    //     $this->submission->set('consent_field', false);

    //     config(['mailchimp.forms' => $formConfig]);

    //     $subscriber = new Subscriber($this->submission->data(), $formConfig);

    //     $consent = $subscriber->hasConsent();

    //     $this->assertFalse($consent);
    // }

    // /** @test */
    // public function consent_when_default_consent_field_is_true()
    // {
    //     $formConfig = [
    //         'form' => 'post',
    //         'check_consent' => true,
    //     ];

    //     $this->submission->set('consent', true);

    //     config(['mailchimp.forms' => $formConfig]);

    //     $subscriber = new Subscriber($this->submission->data(), $formConfig);

    //     $consent = $subscriber->hasConsent();

    //     $this->assertTrue($consent);
    // }

    // /** @test */
    // public function consent_when_configured_consent_field_is_true()
    // {
    //     $formConfig =
    //         [
    //             'blueprint' => 'post',
    //             'check_consent' => true,
    //             'consent_field' => 'the-consent',
    //         ];

    //     $this->submission->set('the-consent', true);

    //     config(['mailchimp.forms' => $formConfig]);

    //     $subscriber = new Subscriber($this->submission->data(), $formConfig);

    //     $consent = $subscriber->hasConsent();

    //     $this->assertTrue($consent);
    // }
}
