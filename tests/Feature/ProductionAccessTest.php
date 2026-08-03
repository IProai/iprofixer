<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\ProductionAccessSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

it('renders the production administrator login page', function (): void {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Authorized administration access only.')
        ->assertSee('Sign in');
});

it('authenticates an active administrator and redirects to RFQs', function (): void {
    $user = User::factory()->create([
        'email' => 'admin@example.test',
        'password' => Hash::make('correct-password'),
        'is_active' => true,
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'correct-password',
    ])->assertRedirect(route('admin.rfqs.index'));

    $this->assertAuthenticatedAs($user);
});

it('rejects inactive administrator accounts', function (): void {
    $user = User::factory()->create([
        'email' => 'inactive@example.test',
        'password' => Hash::make('correct-password'),
        'is_active' => false,
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'correct-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('codifies RFQ management access for production administrator roles', function (): void {
    $this->seed(ProductionAccessSeeder::class);

    expect(Role::findByName('Content Administrator')->hasPermissionTo('rfq.manage'))->toBeTrue()
        ->and(Role::findByName('Sales Administrator')->hasPermissionTo('rfq.manage'))->toBeTrue();
});
