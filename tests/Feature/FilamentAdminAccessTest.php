<?php

use App\Models\User;
use Database\Seeders\DefaultAdminSeeder;
use Filament\Facades\Filament;

it('allows admin users to access the admin panel', function () {
    $user = User::factory()->admin()->create();
    $panel = Filament::getPanel('admin');

    expect($user->canAccessPanel($panel))->toBeTrue();
});

it('denies regular users access to the admin panel', function () {
    $user = User::factory()->create();
    $panel = Filament::getPanel('admin');

    expect($user->canAccessPanel($panel))->toBeFalse();
});

it('seeds a default admin user once', function () {
    $this->seed(DefaultAdminSeeder::class);
    $this->seed(DefaultAdminSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->first();

    expect($admin)
        ->not->toBeNull()
        ->and($admin->name)->toBe('Default Admin')
        ->and($admin->is_admin)->toBeTrue()
        ->and(User::query()->where('email', 'admin@example.com')->count())->toBe(1);
});
