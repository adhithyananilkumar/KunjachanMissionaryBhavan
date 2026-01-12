<?php

use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function(){
    Storage::fake('public');
});

it('stores new inmate with generated admission number', function(){
    $admin = User::factory()->create(['role' => 'system_admin']);
    $inst = Institution::factory()->create();

    $this->actingAs($admin)
        ->post(route('system_admin.inmates.store'), [
            'institution_id' => $inst->id,
            'type' => 'elderly',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1970-01-15',
            'gender' => 'Male',
            'admission_date' => '2025-09-25',
            'photo' => UploadedFile::fake()->image('p.jpg'),
        ])->assertRedirect();

    $inmate = \App\Models\Inmate::latest()->first();
    expect($inmate)->not->toBeNull();
    expect($inmate->admission_number)->toStartWith('ADM'.now()->format('Y'));
    // Photo stored under admission-based dir
    if ($inmate->photo_path) {
        expect($inmate->photo_path)->toContain($inmate->admission_number);
        Storage::disk('public')->assertExists($inmate->photo_path);
    }
});
