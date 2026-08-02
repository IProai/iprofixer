<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class ContactController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('contact.view'), 403);

        $contacts = Contact::query()
            ->with(['organization', 'property'])
            ->when($request->input('search'), fn ($q, $search) => $q->where(
                fn ($sub) => $sub->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(25);

        return view('admin.contacts.index', compact('contacts'));
    }

    public function show(Request $request, Contact $contact): View
    {
        abort_unless($request->user()?->can('contact.view'), 403);

        $contact->load(['organization', 'property', 'leads']);

        return view('admin.contacts.show', compact('contact'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('contact.create'), 403);

        $validated = $request->validate([
            'organization_id' => ['required', 'uuid', 'exists:organizations,id'],
            'property_id' => ['nullable', 'uuid', 'exists:properties,id'],
            'salutation' => ['nullable', 'string', 'max:16'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'role_type' => ['required', 'string', 'in:decision_maker,influencer,stakeholder,procurement,fb_manager'],
            'is_primary' => ['nullable', 'boolean'],
            'locale' => ['nullable', 'string', 'in:en,ar'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $contact = DB::transaction(function () use ($validated, $request): Contact {
            $c = Contact::query()->create([
                ...$validated,
                'email' => isset($validated['email']) ? Str::lower($validated['email']) : null,
                'is_primary' => $validated['is_primary'] ?? false,
                'created_by' => $request->user()->getKey(),
            ]);

            DB::table('audit_events')->insert([
                'id' => (string) Str::uuid(),
                'actor_id' => $request->user()->getKey(),
                'action' => 'crm.contact.created',
                'subject_type' => Contact::class,
                'subject_id' => (string) $c->id,
                'correlation_id' => (string) Str::uuid(),
                'before' => null,
                'after' => json_encode(['name' => $c->full_name, 'email' => $c->email], JSON_THROW_ON_ERROR),
                'metadata' => null,
                'occurred_at' => now(),
            ]);

            return $c;
        });

        return redirect()
            ->route('admin.contacts.show', $contact)
            ->with('status', 'Contact created.');
    }
}
