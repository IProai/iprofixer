<?php

declare(strict_types=1);

use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('denies RFQ reporting without explicit permission', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.rfqs.report'))
        ->assertForbidden();
});

it('reports deterministic RFQ workload response and closure metrics', function (): void {
    config()->set('iprofixer.rfq_sla.first_response_hours', 4);
    config()->set('iprofixer.rfq_sla.stale_contact_hours', 48);

    $operator = User::factory()->create();
    $owner = User::factory()->create(['name' => 'Commercial Owner']);
    $permission = Permission::create(['name' => 'rfq.manage', 'guard_name' => 'web']);
    $operator->givePermissionTo($permission);

    $createRfq = function (array $attributes): FormSubmission {
        return FormSubmission::query()->create(array_merge([
            'reference' => 'RFQ-'.Str::upper(Str::random(10)),
            'type' => 'rfq',
            'status' => 'new',
            'locale' => 'en',
            'contact_name' => 'Procurement Contact',
            'email' => Str::random(8).'@example.test',
            'correlation_id' => (string) Str::uuid(),
            'submitted_at' => now(),
        ], $attributes));
    };

    $breached = $createRfq([
        'reference' => 'RFQ-BREACHED-001',
        'assigned_to' => null,
        'submitted_at' => now()->subHours(6),
        'last_contacted_at' => null,
    ]);

    $createRfq([
        'reference' => 'RFQ-STALE-001',
        'status' => 'in_progress',
        'assigned_to' => $owner->getKey(),
        'submitted_at' => now()->subDays(4),
        'last_contacted_at' => now()->subHours(72),
    ]);

    $createRfq([
        'reference' => 'RFQ-WON-001',
        'status' => 'closed_won',
        'assigned_to' => $owner->getKey(),
        'last_contacted_at' => now()->subHour(),
    ]);

    $createRfq([
        'reference' => 'RFQ-LOST-001',
        'status' => 'closed_lost',
        'assigned_to' => $owner->getKey(),
        'last_contacted_at' => now()->subHour(),
    ]);

    $response = $this->actingAs($operator)
        ->get(route('admin.rfqs.report'));

    $response
        ->assertOk()
        ->assertSee('RFQ operations report')
        ->assertSee('First-response breaches')
        ->assertSee('Stale follow-ups')
        ->assertSee('50%')
        ->assertSee('RFQ-BREACHED-001')
        ->assertSee('RFQ-STALE-001')
        ->assertSee('Commercial Owner');

    expect($breached->last_contacted_at)->toBeNull();
});
