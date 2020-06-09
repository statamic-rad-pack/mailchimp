<?php

namespace Edalzell\Mailchimp\Tests\Unit;

use DrewM\MailChimp\MailChimp;
use Edalzell\Mailchimp\Tests\TestCase;

class MiscTest extends TestCase
{
    /** @test */
    public function can_initialize_mailchimp()
    {
        config(['mailchimp.key' => env('MAILCHIMP_KEY')]);

        $client = app(MailChimp::class);

        $this->assertInstanceOf(MailChimp::class, $client);
    }
}
