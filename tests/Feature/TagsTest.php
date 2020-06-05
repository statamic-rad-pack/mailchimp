<?php

namespace Silentz\Charge\Tests\Feature;

use Laravel\Cashier\Subscription;
use Silentz\Charge\Models\User;
use Silentz\Charge\Tags\Subscription as SubscriptionTag;
use Statamic\Facades\Antlers;

class TagsTest extends FeatureTestCase
{
    /** @var User */
    private $user;

    /** @var Subscription */
    private $subscription;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(__DIR__.'/database/factories');

        $this->user = factory(User::class)->create();

        $this->subscription = factory(Subscription::class)->make([
            'stripe_status' => 'active',
        ]);

        $this->user->subscriptions()->save($this->subscription);
    }

    /** @test */
    public function can_cancel_subscription()
    {
        $tag = (new SubscriptionTag())
            ->setParser(Antlers::parser())
            ->setContext([])
            ->setParameters(['name' => $this->subscription->name]);

        $html = $tag->cancel();

        $this->assertStringContainsString(
            route('statamic.charge.subscription.cancel', [
                'name' => $this->subscription->name,
            ]),
            $html
        );
        $this->assertStringContainsString('_token', $html);
    }

    public function cant_get_subscription_thats_not_yours()
    {
        $user = $this->createCustomer('subscriptions_can_be_created');
        $subscription = $user
            ->newSubscription('test-subscription', static::$planId)
            ->create('pm_card_visa');

        $this
            ->actingAs($this->createCustomer('no-subscriptions'))
            ->get(route('statamic.charge.subscriptions.show', [
                'name' => $subscription->id,
            ]))->assertForbidden();
    }
}
