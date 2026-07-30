<?php

declare(strict_types=1);

use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('denies the RFQ workspace without explicit permission', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/rfqs')->assertForbidden();
});

it('allows an authorised operator to assign and progress an RFQ with audit evidence', function (): void {
    $operator = User::factory()->create();
    $assignee = User::factory()->create();
    $permission = Permission::create(['name' => 'rfq.manage', 'guard_name' => 'web']);
    $operator->givePermissionTo($permission);

    $rfq = FormSubmission::query()->create([
        'reference' => 'RFQ-20260731-ABC123',
        'type' => 'rfq',
        'status' => 'new',
        'locale' => 'en',
        'contact_name' => 'Hotel Operations',
        'email' => 'operations@example.test',
        'correlation_id' => (string) Str::uuid(),
        'submitted_at' => now(),
    ]);

    $this->actingAs($operator)
        ->get("/admin/rfqs/{$rfq->getKey()}")
        ->assertOk()
        ->assertSee('RFQ-20260731-ABC123');

    $this->actingAs($operator)
        ->put("/admin/rfqs/{$rfq->getKey()}", [
            'status' => 'qualified',
            'assigned_to' => $assignee->getKey(),
            'mark_contacted' => true,
        ])
        ->assertRedirect();

    $rfq->refresh();

    expect($rfq->status)->toBe('qualified')
        ->and($rfq->assigned_to)->toBe($assignee->getKey())
        ->and($rfq->last_contacted_at)->not->toBeNull();

    $this->assertDatabaseHas('audit_events', [
        'action' => 'rfq.workflow.updated',
        'subject_id' => (string) $rfq->getKey(),
        'actor_id' => $operator->getKey(),
    ]);
});
