<?php

namespace Silentz\Charge\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Silentz\Charge\Tests\Feature\FeatureTestCase as TestCase;

class CustomerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    /** @test */
    public function routes_exist()
    {
        $routes = Route::getRoutes();

        $this->assertTrue(
            $routes->hasNamedRoute('statamic.charge.customer.update')
        );

        $this->assertTrue(
            $routes->hasNamedRoute('statamic.charge.customer.show')
        );
    }

    /** @test */
    public function redirected_to_login_when_logged_out()
    {
        $user = $this->createCustomer('customers_can_be_created');

        $this->get(
            route('statamic.charge.customer.show', ['user'=> $user->id])
        )->assertRedirect(route('login'));
    }

    /** @test */
    public function can_update_payment_method()
    {
        $user = $this->createCustomer('payment_method_can_be_updated');
        $user->createAsStripeCustomer();
        $pm = $user->defaultPaymentMethod();

        $response = $this->actingAs($user)->patch(
            route('statamic.charge.customer.update'),
            ['payment_method' => 'pm_card_visa']
        );

        $response->assertOk();
        $this->assertNotEquals($pm, $user->defaultPaymentMethod());
    }
}
