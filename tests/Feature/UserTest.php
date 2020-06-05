<?php

namespace Silentz\Charge\Tests\Feature;

use Illuminate\Support\Facades\Config;
use Statamic\Auth\User;
use Statamic\Facades\Role;

class UserTest extends FeatureTestCase
{
    /** @test */
    public function can_add_and_swap_roles()
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

        $user = $this->createCustomer('swap-roles');
        $user->stripe_id = 'swap-roles';
        $user->save();

        $user->swapPlans('plan-one');

        $statamicUser = User::fromUser($user);

        $this->assertTrue($statamicUser->hasRole('role-one'));
        $this->assertFalse($statamicUser->hasRole('role-two'));

        $user->swapPlans('plan-two', 'plan-one');

        $statamicUser = User::fromUser($user);

        $this->assertFalse($statamicUser->hasRole('role-one'));
        $this->assertTrue($statamicUser->hasRole('role-two'));
    }
}
