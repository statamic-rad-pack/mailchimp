<?php

namespace Silentz\Charge\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Statamic\Facades\Role;
use Stripe\Coupon;
use Stripe\Plan;
use Stripe\Product;

class SubscriptionTest extends FeatureTestCase
{
    /**
     * @var string
     */
    protected static $productId;

    /**
     * @var string
     */
    protected static $planId;

    /**
     * @var string
     */
    protected static $otherPlanId;

    /**
     * @var string
     */
    protected static $premiumPlanId;

    /**
     * @var string
     */
    protected static $couponId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$productId =
            static::$stripePrefix.'product-1'.Str::random(10);
        static::$planId =
            static::$stripePrefix.'monthly-10-'.Str::random(10);
        static::$otherPlanId =
            static::$stripePrefix.'monthly-10-'.Str::random(10);
        static::$premiumPlanId =
            static::$stripePrefix.'monthly-20-premium-'.Str::random(10);
        static::$couponId = static::$stripePrefix.'coupon-'.Str::random(10);

        Product::create([
            'id' => static::$productId,
            'name' => 'Laravel Cashier Test Product',
            'type' => 'service',
        ]);

        Plan::create([
            'id' => static::$planId,
            'nickname' => 'Monthly $10',
            'currency' => 'USD',
            'interval' => 'month',
            'billing_scheme' => 'per_unit',
            'amount' => 1000,
            'product' => static::$productId,
        ]);

        Plan::create([
            'id' => static::$otherPlanId,
            'nickname' => 'Monthly $10 Other',
            'currency' => 'USD',
            'interval' => 'month',
            'billing_scheme' => 'per_unit',
            'amount' => 1000,
            'product' => static::$productId,
        ]);

        Plan::create([
            'id' => static::$premiumPlanId,
            'nickname' => 'Monthly $20 Premium',
            'currency' => 'USD',
            'interval' => 'month',
            'billing_scheme' => 'per_unit',
            'amount' => 2000,
            'product' => static::$productId,
        ]);

        Coupon::create([
            'id' => static::$couponId,
            'duration' => 'repeating',
            'amount_off' => 500,
            'duration_in_months' => 3,
            'currency' => 'USD',
        ]);

        //Role::make('foo');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        static::deleteStripeResource(new Plan(static::$planId));
        static::deleteStripeResource(new Plan(static::$otherPlanId));
        static::deleteStripeResource(new Plan(static::$premiumPlanId));
        static::deleteStripeResource(new Product(static::$productId));
        static::deleteStripeResource(new Coupon(static::$couponId));
    }

    /** @test */
    public function routes_exist()
    {
        $routes = Route::getRoutes();

        $this->assertTrue(
            $routes->hasNamedRoute('statamic.charge.subscriptions.store')
        );
        $this->assertTrue(
            $routes->hasNamedRoute('statamic.charge.subscriptions.update')
        );

        $this->assertTrue(
            $routes->hasNamedRoute('statamic.charge.subscriptions.destroy')
        );

        $this->assertTrue($routes->hasNamedRoute('statamic.charge.webhook'));
    }

    /** @test */
    public function redirected_to_login_when_logged_out()
    {
        $this->post(
            route('statamic.charge.subscriptions.store')
        )->assertRedirect(route('login'));
    }

    /** @test */
    public function checks_for_required_input()
    {
        $user = $this->createCustomer('subscriptions_can_be_created');

        $response = $this->actingAs($user)->post(
            route('statamic.charge.subscriptions.store'),
            []
        );

        $response->assertSessionHasErrors([
            'name',
            'plan',
            'payment_method',
        ]);
    }

    /** @test */
    public function can_create_simple_subscription()
    {
        $user = $this->createCustomer('subscriptions_can_be_created');

        $this->actingAs($user)->post(route('statamic.charge.subscriptions.store'), [
            'name' => 'test-subscription',
            'plan' => static::$planId,
            'payment_method' => 'pm_card_visa',
        ])->assertRedirect();

        $this->assertTrue($user->subscribed('test-subscription'));
    }

    /** @test */
    public function can_cancel_at_end_of_period()
    {
        $user = $this->createCustomer('canceled-at-end-of-period');

        $subscription = $user
            ->newSubscription(
                'test-cancel-subscription-at-period-end',
                static::$planId
            )
            ->create('pm_card_visa');

        $response = $this->actingAs($user)->delete(
            route('statamic.charge.subscriptions.destroy', [
                'subscription' => $subscription->id,
            ])
        );
        $response->assertRedirect();

        $this->assertTrue(
            $user
                ->subscription('test-cancel-subscription-at-period-end')
                ->onGracePeriod()
        );
        $this->assertTrue(
            $user
                ->subscription('test-cancel-subscription-at-period-end')
                ->cancelled()
        );
    }

    /** @test */
    public function can_cancel_immediately()
    {
        $user = $this->createCustomer('cancel-immediately');

        $subscription = $user
            ->newSubscription(
                'test-cancel-subscription-immediately',
                static::$planId
            )
            ->create('pm_card_visa');

        $response = $this->actingAs($user)->delete(
            route('statamic.charge.subscriptions.destroy', [
                'subscription' => $subscription->id,
            ]),
            [
                'cancel_immediately' => true,
            ]
        );
        $response->assertRedirect();

        $this->assertTrue(
            $user
                ->subscription('test-cancel-subscription-immediately')
                ->cancelled()
        );

        $this->assertFalse(
            $user
                ->subscription('test-cancel-subscription-immediately')
                ->onGracePeriod()
        );
    }

    /** @test */
    public function cant_cancel_subscription_not_yours()
    {
        $user1 = $this->createCustomer('has-subscription');
        $user2 = $this->createCustomer('has-no-subscription');

        $subscription = $user1
            ->newSubscription(
                'default',
                static::$planId
            )
            ->create('pm_card_visa');

        $this
            ->actingAs($user2)->delete(
                route('statamic.charge.subscriptions.destroy', ['subscription' => $subscription->id])
            )->assertForbidden();
    }

    /** @test */
    public function can_edit_subscription()
    {
        $user = $this->createCustomer('edit-subscription');

        $subscription = $user
            ->newSubscription('edit-subscription', static::$planId)
            ->create('pm_card_visa');

        $this
            ->actingAs($user)->patch(
                route('statamic.charge.subscriptions.update', ['subscription' => $subscription->id]),
                [
                    'plan' => static::$premiumPlanId,
                    'quantity' => 3,
                ]
            )->assertRedirect();

        $subscription = $user->subscription('edit-subscription');

        $this->assertEquals(static::$premiumPlanId, $subscription->stripe_plan);
        $this->assertEquals(3, $subscription->quantity);
    }

    /** @test */
    public function will_redirect_on_successful_subscription_cancellation()
    {
        $user = $this->createCustomer('canceled-at-end-of-period');

        $subscription = $user
            ->newSubscription(
                'test-cancel-subscription-at-period-end',
                static::$planId
            )
            ->create('pm_card_visa');

        $response = $this->actingAs($user)->delete(
            route('statamic.charge.subscriptions.destroy', [
                'subscription' => $subscription->id,
            ]),
            [
                'redirect' => '/cancel/success',
            ]
        );

        $response->assertRedirect('/cancel/success');
    }
}
