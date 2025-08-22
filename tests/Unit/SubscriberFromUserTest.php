<?php

namespace StatamicRadPack\Mailchimp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\User;
use StatamicRadPack\Mailchimp\Subscriber;
use StatamicRadPack\Mailchimp\Tests\TestCase;

class SubscriberFromUserTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::make()
            ->email('foo@bar.com')
            ->password('password');
    }

    #[Test]
    public function can_create_subscriber_from_user()
    {
        $config = [[
            'check_consent' => true,
        ]];

        $subscriber = new Subscriber($this->userData(), $config);

        $this->assertInstanceOf(Subscriber::class, $subscriber);
    }

    #[Test]
    public function has_consent_by_default()
    {
        $subscriber = new Subscriber($this->userData(), []);

        $this->assertTrue($subscriber->hasConsent());
    }

    #[Test]
    public function no_consent_when_no_consent_field()
    {
        $config = [
            'check_consent' => true,
        ];

        $subscriber = new Subscriber($this->userData(), $config);

        $this->assertFalse($subscriber->hasConsent());
    }

    // #[Test]
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

    // #[Test]
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

    // #[Test]
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

    private function userData(): array
    {
        return $this->user->data()->merge(['email' => $this->user->email()])->all();
    }
}
