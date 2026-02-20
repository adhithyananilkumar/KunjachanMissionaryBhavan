<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInmateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if(!$user) return false;
        // Route group already protected by role middleware; this is a secondary gate.
        return $user->hasAnyRole(['system_admin','developer']);
    }

    public function rules(): array
    {
        $rules = [
            // Allow either legacy pattern ADMYYYY###### or a plain integer (1-20 digits)
            'admission_number' => ['required','string','max:32','regex:/^(ADM\d{10}|\d{1,20})$/','unique:inmates,admission_number'],
            'admission_date' => ['required','date'],
            'institution_id' => ['required','exists:institutions,id'],
            'type' => ['required','in:child,elderly,mental_health,rehabilitation'],
            'first_name' => ['required','string','max:255'],
            'last_name' => ['nullable','string','max:255'],
            'date_of_birth' => ['required','date'],
            'gender' => ['required','in:Male,Female,Other'],
            'marital_status' => ['nullable','in:Single,Married,Separated,Divorced,Widowed'],
            'blood_group' => ['nullable','string','max:10'],
            'height' => ['nullable','numeric','min:0','max:300'],
            'weight' => ['nullable','numeric','min:0','max:500'],
            'identification_marks' => ['nullable','string'],
            'religion' => ['nullable','string','max:100'],
            'caste' => ['nullable','string','max:100'],
            'nationality' => ['nullable','string','max:100'],
            'address' => ['nullable'],
            'father_name' => ['nullable','string','max:255'],
            'mother_name' => ['nullable','string','max:255'],
            'spouse_name' => ['nullable','string','max:255'],
            'guardian_name' => ['nullable','string','max:255'],
            'guardian_relation' => ['nullable','string','max:100'],
            'guardian_email' => ['nullable','email','max:255'],
            'guardian_phone' => ['nullable','string','max:50'],
            'guardian_address' => ['nullable','string'],
            'education_details' => ['nullable'],
            'notes' => ['nullable','string'],
            'case_notes' => ['nullable','string'],
            'health_info' => ['nullable'],
            'aadhaar_number' => ['nullable','string','max:100'],
            'photo' => ['nullable','file','mimes:jpg,jpeg,png,webp,heic,heif','max:8192'],
            'aadhaar_card' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp,heic,heif','max:10240'],
            'ration_card' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp,heic,heif','max:10240'],
            'panchayath_letter' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp,heic,heif','max:10240'],
            'disability_card' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp,heic,heif','max:10240'],
            'doctor_certificate' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp,heic,heif','max:10240'],
            'vincent_depaul_card' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp,heic,heif','max:10240'],
            'doc_names.*' => ['nullable','string','max:255'],
            'doc_files.*' => ['nullable','file','max:10240'],
            'location_id' => ['nullable','exists:locations,id'],
            'admitted_by' => ['nullable','exists:users,id'],
            'verified_by' => ['nullable','exists:users,id'],
        ];

        // Conditional: child requires father/mother or guardian
        if ($this->input('type') === 'child') {
            $rules['father_name'][] = 'required_without:guardian_name';
            $rules['mother_name'][] = 'required_without:guardian_name';
        }
        // Conditional: adult/elderly require marital_status
        if (in_array($this->input('type'), ['elderly','rehabilitation'])) {
            $rules['marital_status'][0] = 'required'; // replace nullable with required
        }

        return $rules;
    }

    public function messages(): array
    {
        $docTypes = 'PDF, JPG, JPEG, PNG, WEBP, HEIC, HEIF';
        return [
            'photo.mimes' => 'Photo must be a JPG, JPEG, PNG, WEBP, HEIC, or HEIF file.',
            'photo.max' => 'Photo must not be larger than 8 MB.',
            'aadhaar_card.mimes' => "Aadhaar card must be one of: {$docTypes}.",
            'ration_card.mimes' => "Ration card must be one of: {$docTypes}.",
            'panchayath_letter.mimes' => "Panchayath letter must be one of: {$docTypes}.",
            'disability_card.mimes' => "Disability card must be one of: {$docTypes}.",
            'doctor_certificate.mimes' => "Doctor certificate must be one of: {$docTypes}.",
            'vincent_depaul_card.mimes' => "Vincent DePaul card must be one of: {$docTypes}.",
            'aadhaar_card.max' => 'Document must not be larger than 10 MB.',
            'ration_card.max' => 'Document must not be larger than 10 MB.',
            'panchayath_letter.max' => 'Document must not be larger than 10 MB.',
            'disability_card.max' => 'Document must not be larger than 10 MB.',
            'doctor_certificate.max' => 'Document must not be larger than 10 MB.',
            'vincent_depaul_card.max' => 'Document must not be larger than 10 MB.',
            'doc_files.*.max' => 'Each extra document must not be larger than 10 MB.',
        ];
    }
}
