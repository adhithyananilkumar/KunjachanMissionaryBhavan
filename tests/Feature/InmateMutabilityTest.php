<?php

use App\Models\Inmate;
use App\Models\Institution;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

it('blocks inmate-scoped mutations when inmate is discharged', function () {
    $institution = Institution::factory()->create();

    $admin = User::factory()->create([
        'role' => 'admin',
        'institution_id' => $institution->id,
    ]);

    $inmate = Inmate::factory()->create([
        'institution_id' => $institution->id,
        'status' => Inmate::STATUS_DISCHARGED,
        'created_by' => $admin->id,
        'updated_by' => $admin->id,
    ]);

    actingAs($admin);

    // This would normally validate and proceed, but should be blocked by middleware before validation.
    postJson(route('admin.inmates.assign-location', $inmate), [
        'location_id' => null,
    ])->assertStatus(403);
});

it('blocks non-inmate route mutations when inmate_id points to discharged inmate', function () {
    $institution = Institution::factory()->create([
        'doctor_assignment_enabled' => false,
    ]);

    $doctor = User::factory()->create([
        'role' => 'doctor',
        'institution_id' => $institution->id,
    ]);

    $inmate = Inmate::factory()->create([
        'institution_id' => $institution->id,
        'status' => Inmate::STATUS_DISCHARGED,
        'created_by' => $doctor->id,
        'updated_by' => $doctor->id,
    ]);

    actingAs($doctor);

    postJson(route('doctor.appointments.store'), [
        'inmate_id' => $inmate->id,
        'title' => 'Checkup',
        'scheduled_for' => now()->toDateString(),
    ])->assertStatus(403);
});
