<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class DuplicateDetectionService
{
    private const SIMILARITY_THRESHOLD = 80;

    /**
     * Find organizations that are potential duplicates of the given name.
     *
     * @return Collection<int, Organization>
     */
    public function findDuplicateOrganizations(string $name): Collection
    {
        $normalized = $this->normalizeName($name);
        if ($normalized === '') {
            return new Collection;
        }

        return Organization::query()
            ->get()
            ->filter(function (Organization $org) use ($name, $normalized): bool {
                $orgNorm = $this->normalizeName($org->name);

                if ($orgNorm === $normalized || Str::lower($org->name) === Str::lower($name)) {
                    return true;
                }

                similar_text($orgNorm, $normalized, $percent);

                return $percent >= self::SIMILARITY_THRESHOLD;
            })
            ->values();
    }

    /**
     * Find contacts that may be duplicates of a given email and name.
     *
     * @return Collection<int, Contact>
     */
    public function findDuplicateContacts(string $email, string $name): Collection
    {
        if ($email !== '') {
            $byEmail = Contact::query()
                ->whereRaw('LOWER(email) = ?', [Str::lower($email)])
                ->get();

            if ($byEmail->isNotEmpty()) {
                return $byEmail;
            }
        }

        $normalized = $this->normalizeName($name);
        if ($normalized === '') {
            return new Collection;
        }

        return Contact::query()
            ->get()
            ->filter(function (Contact $contact) use ($normalized): bool {
                $fullName = $this->normalizeName($contact->first_name.' '.$contact->last_name);
                similar_text($fullName, $normalized, $percent);

                return $percent >= self::SIMILARITY_THRESHOLD;
            })
            ->values();
    }

    private function normalizeName(string $name): string
    {
        $name = preg_replace('/\b(LLC|Ltd|LTD|Inc|Corp|FZ|LLC-FZ|FZCO|DMCC)\b/i', '', (string) $name);
        $name = preg_replace('/[^a-zA-Z0-9\s]/', ' ', (string) $name);

        return Str::squish(Str::lower((string) $name));
    }
}
