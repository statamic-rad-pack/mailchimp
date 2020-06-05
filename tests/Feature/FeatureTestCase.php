<?php

namespace Silentz\Charge\Tests\Feature;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silentz\Charge\Tests\TestCase;
use Stripe\ApiResource;
use Stripe\Exception\InvalidRequestException;
use Stripe\Stripe;

abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    /**
     * @var string
     */
    protected static $stripePrefix = 'charge-test-';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Stripe::setApiKey(getenv('STRIPE_SECRET'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        Eloquent::unguard();

        $this->loadMigrationsFrom(__DIR__.'/../__migrations__');
    }

    protected static function deleteStripeResource(ApiResource $resource)
    {
        try {
            $resource->delete();
        } catch (InvalidRequestException $e) {
        }
    }
}
