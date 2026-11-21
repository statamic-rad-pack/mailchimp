<?php

namespace StatamicRadPack\Mailchimp\Tests\Unit;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use StatamicRadPack\Mailchimp\Facades\Newsletter;
use StatamicRadPack\Mailchimp\Subscriber;
use StatamicRadPack\Mailchimp\Tests\TestCase;

class SubscriberLoggingTest extends TestCase
{
    #[Test]
    public function failed_subscription_does_not_log_the_api_key()
    {
        config(['mailchimp.api_key' => 'abc123-us7']);

        $body = '{"title":"Forgotten Email Not Subscribed","status":400}';

        $api = Mockery::mock(MailChimp::class);
        $api->shouldReceive('getLastResponse')->andReturn([
            'headers' => [
                'http_code' => 400,
                'request_header' => "PUT /3.0/lists/abc/members/def HTTP/1.1\r\nAuthorization: apikey secret-api-key-us7\r\n",
            ],
            'body' => $body,
        ]);

        Newsletter::shouldReceive('subscribeOrUpdate')->once()->andReturnFalse();
        Newsletter::shouldReceive('getLastError')->andReturn('400: Forgotten Email Not Subscribed');
        Newsletter::shouldReceive('getApi')->andReturn($api);

        Log::spy();

        (new Subscriber(['email' => 'foo@bar.com'], ['form' => 'contact_us']))->subscribe();

        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(fn ($message, $context) => $message === '400: Forgotten Email Not Subscribed'
                && $context === ['body' => $body]
                && ! str_contains(json_encode($context), 'secret-api-key'));
    }
}
