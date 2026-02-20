<?php

namespace App\Services;

use App\Models\Inmate;
use App\Models\InmateStatusEvent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InmateLifecycleService
{
    public function discharge(Inmate $inmate, int $actorUserId, array $data): InmateStatusEvent
    {
        return $this->transition(
            inmate: $inmate,
            actorUserId: $actorUserId,
            eventType: 'discharge',
            toStatus: Inmate::STATUS_DISCHARGED,
            data: $data,
        );
    }

    public function transfer(Inmate $inmate, int $actorUserId, array $data): InmateStatusEvent
    {
        return $this->transition(
            inmate: $inmate,
            actorUserId: $actorUserId,
            eventType: 'transfer',
            toStatus: Inmate::STATUS_TRANSFERRED,
            data: $data,
        );
    }

    public function markDeceased(Inmate $inmate, int $actorUserId, array $data): InmateStatusEvent
    {
        return $this->transition(
            inmate: $inmate,
            actorUserId: $actorUserId,
            eventType: 'deceased',
            toStatus: Inmate::STATUS_DECEASED,
            data: $data,
        );
    }

    public function rejoin(Inmate $inmate, int $actorUserId, array $data): InmateStatusEvent
    {
        return $this->transition(
            inmate: $inmate,
            actorUserId: $actorUserId,
            eventType: 'rejoin',
            toStatus: Inmate::STATUS_PRESENT,
            data: $data,
        );
    }

    public function addDeathCertificate(Inmate $inmate, int $actorUserId, array $data): InmateStatusEvent
    {
        return DB::transaction(function () use ($inmate, $actorUserId, $data) {
            $fresh = Inmate::query()->whereKey($inmate->getKey())->lockForUpdate()->firstOrFail();

            $status = $fresh->status ?: Inmate::STATUS_PRESENT;
            if ($status !== Inmate::STATUS_DECEASED) {
                throw ValidationException::withMessages([
                    'status' => 'Death certificate upload is only allowed for deceased inmates.',
                ]);
            }

            $attachments = Arr::get($data, 'attachments', []);
            if (empty($attachments)) {
                throw ValidationException::withMessages([
                    'death_certificate' => 'Death certificate file is required.',
                ]);
            }

            return InmateStatusEvent::create([
                'inmate_id' => $fresh->id,
                'event_type' => 'death_certificate_added',
                'from_status' => Inmate::STATUS_DECEASED,
                'to_status' => Inmate::STATUS_DECEASED,
                'effective_at' => Arr::get($data, 'effective_at', now()),
                'reason' => Arr::get($data, 'reason'),
                'meta' => Arr::get($data, 'meta'),
                'attachments' => $attachments,
                'created_by' => $actorUserId,
                'created_at' => now(),
            ]);
        });
    }

    private function transition(Inmate $inmate, int $actorUserId, string $eventType, string $toStatus, array $data): InmateStatusEvent
    {
        return DB::transaction(function () use ($inmate, $actorUserId, $eventType, $toStatus, $data) {
            $fresh = Inmate::query()->whereKey($inmate->getKey())->lockForUpdate()->firstOrFail();

            $fromStatus = $fresh->status ?: Inmate::STATUS_PRESENT;

            $this->assertAllowedTransition($fromStatus, $toStatus, $eventType);

            $fresh->status = $toStatus;

            // Cross-facility transfers may update institution_id (system admin only; validated in controller)
            if ($eventType === 'transfer' && !empty($data['to_institution_id'])) {
                $fresh->institution_id = (int) $data['to_institution_id'];
            }

            // Leaving "present" should free up the current allocation
            if ($toStatus !== Inmate::STATUS_PRESENT) {
                try {
                    $current = $fresh->currentLocation()->first();
                    if ($current) {
                        $current->end_date = now();
                        $current->save();
                    }
                } catch (\Throwable $e) {
                    // Allocation cleanup is best-effort; do not block status change.
                }

                try {
                    $fresh->room_location_id = null;
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            $fresh->save();

            return InmateStatusEvent::create([
                'inmate_id' => $fresh->id,
                'event_type' => $eventType,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'effective_at' => Arr::get($data, 'effective_at', now()),
                'reason' => Arr::get($data, 'reason'),
                'meta' => Arr::get($data, 'meta'),
                'attachments' => Arr::get($data, 'attachments'),
                'created_by' => $actorUserId,
                'created_at' => now(),
            ]);
        });
    }

    private function assertAllowedTransition(string $fromStatus, string $toStatus, string $eventType): void
    {
        if ($fromStatus === Inmate::STATUS_DECEASED) {
            throw ValidationException::withMessages([
                'status' => 'This inmate is marked as deceased and cannot be changed.',
            ]);
        }

        if ($fromStatus === $toStatus) {
            throw ValidationException::withMessages([
                'status' => 'No status change detected.',
            ]);
        }

        $allowedTo = match ($fromStatus) {
            Inmate::STATUS_PRESENT => [Inmate::STATUS_DISCHARGED, Inmate::STATUS_TRANSFERRED, Inmate::STATUS_DECEASED],
            Inmate::STATUS_DISCHARGED, Inmate::STATUS_TRANSFERRED => [Inmate::STATUS_PRESENT],
            default => [],
        };

        if (!in_array($toStatus, $allowedTo, true)) {
            throw ValidationException::withMessages([
                'status' => "Invalid transition ({$eventType}) from {$fromStatus} to {$toStatus}.",
            ]);
        }
    }
}
