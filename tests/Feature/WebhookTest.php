<?php

namespace Silentz\Charge\Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookHandled;
use Silentz\Charge\Listeners\HandleWebhook;
use Silentz\Charge\Mail\CustomerSubscriptionCanceled;
use Silentz\Charge\Mail\CustomerSubscriptionCreated;
use Silentz\Charge\Mail\CustomerSubscriptionUpdated;
use Silentz\Charge\Mail\CustomerUpdated;
use Silentz\Charge\Mail\InvoicePaymentActionRequired;
use Statamic\Auth\User;
use Statamic\Facades\Role;
use Statamic\Support\Arr;

class WebhookTest extends FeatureTestCase
{
    /** @test */
    public function does_respond_to_events()
    {
        $this->mock(HandleWebhook::class, function ($mock) {
            $mock->shouldReceive('handle')->once();
        });

        WebhookHandled::dispatch([]);
    }

    /** @test */
    public function adds_role_when_subscription_created()
    {
        Role::make('test-role')->title('Test Role')->save();

        $roles[] = [
                'plan' => 'test-plan',
                'role' => 'test-role',
            ];

        Config::set('charge.subscription.roles', $roles);

        Mail::fake();
        Event::fake();

        $user = $this->createCustomer('add-roles');
        $user->stripe_id = 'add-role';
        $user->swapPlans('plan-one');

        $user->save();

        $data = [];

        Arr::set($data, 'type', 'customer.subscription.created');
        Arr::set($data, 'data.object.customer', 'add-role');
        Arr::set($data, 'data.object.plan.id', 'test-plan');

        $this->postJson(route('statamic.charge.webhook'), $data)
                ->assertOk();

        /** @var User */
        $statamicUser = User::fromUser($user);

        $this->assertTrue($statamicUser->hasRole('test-role'));
    }

    /** @test */
    public function swaps_roles_when_subscription_updated()
    {
        Role::make('role-one')->title('Role One')->save();
        Role::make('role-two')->title('Role Two')->save();

        $roles[] = [
            'plan' => 'plan-one',
            'role' => 'role-one',
        ];

        $roles[] = [
            'plan' => 'plan-two',
            'role' => 'role-two',
        ];

        Config::set('charge.subscription.roles', $roles);

        Mail::fake();
        Event::fake();

        $user = $this->createCustomer('add-roles');
        $user->stripe_id = 'add-role';
        $user->save();

        $data = [];

        Arr::set($data, 'type', 'customer.subscription.updated');
        Arr::set($data, 'data.object.customer', 'add-role');
        Arr::set($data, 'data.object.plan.id', 'plan-two');
        Arr::set($data, 'data.previous_attributes.plan.id', 'plan-one');

        $this->postJson(route('statamic.charge.webhook'), $data)
                ->assertOk();

        /** @var User */
        $statamicUser = User::fromUser($user);

        $this->assertFalse($statamicUser->hasRole('role-one'));
        $this->assertTrue($statamicUser->hasRole('role-two'));
    }

    /** @test */
    public function events_do_send_email()
    {
        Mail::fake();

        /*
customer.subscription.created - Occurs whenever a customer is signed up for a new plan.
customer.subscription.deleted - Occurs whenever a customer's subscription ends.
customer.subscription.trial_will_end - Occurs three days before a subscription's trial period is scheduled to end, or when a trial is ended immediately (using trial_end=now).
customer.subscription.updated - Occurs whenever a subscription changes (e.g., switching from one plan to another, or changing the status from trial to active).        */

        $types = [
            'customer.subscription.created' => CustomerSubscriptionCreated::class,
            'customer.subscription.updated' => CustomerSubscriptionUpdated::class,
            'customer.subscription.canceled' => CustomerSubscriptionCanceled::class,
            'customer.updated' => CustomerUpdated::class,
            'invoice.payment_action_required' => InvoicePaymentActionRequired::class,
        ];

        foreach ($types as $type => $class) {
            WebhookHandled::dispatch(['type' => $type]);

            Mail::assertSent($class);
        }
    }
}
