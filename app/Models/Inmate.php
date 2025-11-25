<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inmate extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admission_number','registration_number',
        'first_name','last_name','date_of_birth','gender','admission_date','institution_id',
        'admitted_by','verified_by','consent_signed_at','room_location_id',
        'marital_status','blood_group','height','weight','identification_marks','religion','caste','nationality','address',
        'father_name','mother_name','spouse_name','guardian_name',
        'guardian_id','guardian_relation','guardian_first_name','guardian_last_name','guardian_email','guardian_phone','guardian_address',
        'education_details','documents','notes','case_notes','health_info','critical_alert',
        'aadhaar_number','photo_path','aadhaar_card_path','ration_card_path','panchayath_letter_path','disability_card_path','doctor_certificate_path','vincent_depaul_card_path',
        'type','intake_history','created_by','updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'consent_signed_at' => 'datetime',
        'address' => 'array',
        'education_details' => 'array',
        'documents' => 'array',
        'health_info' => 'array',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function admittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admitted_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function roomLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'room_location_id');
    }

    public function doctor(): BelongsTo
    {
        // Backward compatibility single doctor assignment (legacy)
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'doctor_inmate', 'inmate_id', 'doctor_id')->withTimestamps();
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function documents()
    {
        return $this->hasMany(InmateDocument::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function labTests()
    {
        return $this->hasMany(LabTest::class);
    }

    /**
     * All location assignments (historical) through the pivot table.
     */
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'location_assignments', 'inmate_id', 'location_id')
            ->withPivot(['start_date','end_date'])
            ->withTimestamps();
    }

    // Active location assignment
    public function currentLocation()
    {
        return $this->hasOne(LocationAssignment::class)->whereNull('end_date');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function educationalRecords()
    {
        return $this->hasMany(EducationalRecord::class);
    }

    public function caseLogEntries()
    {
        return $this->hasMany(CaseLogEntry::class)->orderBy('entry_date');
    }

    public function geriatricCarePlan()
    {
        return $this->hasOne(GeriatricCarePlan::class);
    }

    public function therapySessionLogs()
    {
        return $this->hasMany(TherapySessionLog::class)->latest('session_date');
    }

    public function counselingProgressNotes()
    {
        return $this->hasMany(CounselingProgressNote::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class)->latest('observed_at')->latest('created_at');
    }

    public function mentalHealthPlan()
    {
        return $this->hasOne(MentalHealthPlan::class);
    }

    public function rehabilitationPlan()
    {
        return $this->hasOne(RehabilitationPlan::class);
    }

    /**
     * Optional accessor for full display name
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . ($this->last_name ?? '')) ?: $this->first_name;
    }

    public function getAvatarUrlAttribute(): string
    {
        if(!empty($this->photo_path)){
            $disk = \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'));
            if(config('filesystems.default') === 's3'){
                // Cache presigned URL and enable browser caching like User model
                try {
                    $cacheKey = 'inmate:avatar_url:'
                        . sha1((string)$this->id.'|'.(string)$this->photo_path.'|'.(string)optional($this->updated_at)->timestamp.'|'.app()->environment());
                    return cache()->remember($cacheKey, now()->addMinutes(30), function() use ($disk) {
                        return $disk->temporaryUrl(
                            $this->photo_path,
                            now()->addMinutes(90),
                            [ 'ResponseCacheControl' => 'public, max-age=3600, immutable' ]
                        );
                    });
                } catch (\Throwable $e) { /* fallback below */ }
            }
            // Local/public: return URL with version param for cache-busting on updates
            $url = $disk->url($this->photo_path);
            $ver = optional($this->updated_at)->timestamp ?: '';
            return $ver ? ($url.(str_contains($url,'?') ? '&' : '?').'v='.$ver) : $url;
        }
        $name = urlencode($this->full_name ?? 'Inmate');
        return "https://ui-avatars.com/api/?name={$name}&color=ffffff&background=0d6efd";
    }
}