<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\DuplicateDetectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class OrganizationController extends Controller
{
    public function __construct(private readonly DuplicateDetectionService $duplicateDetection) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('organization.view'), 403);

        $organizations = Organization::query()
            ->with(['group', 'properties', 'contacts'])
            ->when($request->input('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->input('search'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(25);

        return view('admin.organizations.index', compact('organizations'));
    }

    public function show(Request $request, Organization $organization): View
    {
        abort_unless($request->user()?->can('organization.view'), 403);

        $organization->load(['group', 'properties', 'contacts', 'leads', 'opportunities']);

        return view('admin.organizations.show', compact('organization'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('organization.create'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:prospect,client,partner,inactive'],
            'group_id' => ['nullable', 'integer', 'exists:organization_groups,id'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $duplicates = $this->duplicateDetection->findDuplicateOrganizations($validated['name']);
        $duplicateStatus = $duplicates->isNotEmpty() ? 'suspected' : 'none';

        $organization = DB::transaction(function () use ($validated, $duplicateStatus, $request): Organization {
            $org = Organization::query()->create([
                ...$validated,
                'duplicate_status' => $duplicateStatus,
                'created_by' => $request->user()->getKey(),
            ]);

            DB::table('audit_events')->insert([
                'id' => (string) Str::uuid(),
                'actor_id' => $request->user()->getKey(),
                'action' => 'crm.organization.created',
                'subject_type' => Organization::class,
                'subject_id' => (string) $org->id,
                'correlation_id' => (string) Str::uuid(),
                'before' => null,
                'after' => json_encode(['name' => $org->name, 'type' => $org->type], JSON_THROW_ON_ERROR),
                'metadata' => json_encode(['duplicate_status' => $duplicateStatus], JSON_THROW_ON_ERROR),
                'occurred_at' => now(),
            ]);

            return $org;
        });

        return redirect()
            ->route('admin.organizations.show', $organization)
            ->with('status', 'Organization created.');
    }

    public function checkDuplicates(Request $request): JsonResponse
    {
        abort_unless($request->user()?->can('organization.view'), 403);

        $name = (string) $request->input('name', '');
        if (trim($name) === '') {
            return response()->json(['duplicates' => []]);
        }

        $duplicates = $this->duplicateDetection->findDuplicateOrganizations($name);

        return response()->json([
            'duplicates' => $duplicates->map(fn (Organization $org) => [
                'id' => $org->id,
                'name' => $org->name,
                'type' => $org->type,
            ]),
        ]);
    }
}
