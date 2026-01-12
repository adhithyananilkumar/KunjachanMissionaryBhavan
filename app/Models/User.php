<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
      * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
          'password',
          'role',
          'institution_id',
          'guardian_id',
          'can_report_bugs',
          'bug_report_enabled',
          'profile_picture_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the institution that the user belongs to.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the medical records for the doctor.
     */
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }

    /**
     * Get the medication logs administered by the nurse.
     */
    public function medicationLogs()
    {
        return $this->hasMany(MedicationLog::class, 'nurse_id');
    }

    /**
     * Get the guardian that the user belongs to.
     */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function actionRequests()
    {
        return $this->hasMany(ActionRequest::class, 'admin_id');
    }

    public function bugReports()
    {
    return $this->hasMany(SupportTicket::class);
    }

    public function therapySessionLogs()
    {
        return $this->hasMany(TherapySessionLog::class,'doctor_id');
    }

    public function counselingProgressNotes()
    {
        return $this->hasMany(CounselingProgressNote::class);
    }

    // Many-to-many assignment with inmates
    public function assignedInmates()
    {
        return $this->belongsToMany(Inmate::class, 'doctor_inmate', 'doctor_id', 'inmate_id')->withTimestamps();
    }

    // Accessor for avatar URL (profile picture or ui-avatars fallback)
    public function getAvatarUrlAttribute(): string
    {
        if(!empty($this->profile_picture_path)){
            $disk = \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'));
            if(config('filesystems.default') === 's3'){
                // Cache the presigned URL to keep it stable across page loads and enable browser caching
                try {
                    $cacheKey = 'avatar_url:'
                        . sha1((string)$this->id.'|'.(string)$this->profile_picture_path.'|'.(string)optional($this->updated_at)->timestamp.'|'.app()->environment());
                    return cache()->remember($cacheKey, now()->addMinutes(30), function() use ($disk) {
                        // Provide Cache-Control on S3 response so browsers can cache the image
                        return $disk->temporaryUrl(
                            $this->profile_picture_path,
                            now()->addMinutes(90),
                            [ 'ResponseCacheControl' => 'public, max-age=3600, immutable' ]
                        );
                    });
                } catch (\Throwable $e) { /* fallback below */ }
            }
            // Local/public: allow browser caching and add a stable version param to bust on update
            $url = $disk->url($this->profile_picture_path);
            $ver = optional($this->updated_at)->timestamp ?: '';
            return $ver ? ($url.(str_contains($url,'?') ? '&' : '?').'v='.$ver) : $url;
        }
        $name = urlencode($this->name ?? 'User');
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF";
    }

    /**
     * Check if user has a role (string compare on 'role' column).
     */
    public function hasRole(string $role): bool
    {
        return (string)$this->role === $role;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array((string)$this->role, $roles, true);
    }
}
