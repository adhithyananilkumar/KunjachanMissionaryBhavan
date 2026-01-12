<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInmateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if(!$user) return false;
        return $user->hasAnyRole(['system_admin','developer']);
    }

    public function rules(): array
    {
        $rules = [
            // Allow legacy pattern or plain integer
            'admission_number' => ['sometimes','nullable','regex:/^(ADM\d{10}|\d{1,20})$/', 'unique:inmates,admission_number,'.($this->route('inmate')?->id)],
            'admission_date' => ['sometimes','date'],
            'institution_id' => ['sometimes','exists:institutions,id'],
            'type' => ['sometimes','in:child,elderly,mental_health,rehabilitation'],
            'first_name' => ['sometimes','string','max:255'],
            'last_name' => ['sometimes','nullable','string','max:255'],
            'date_of_birth' => ['sometimes','date'],
            'gender' => ['sometimes','in:Male,Female,Other'],
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
            'registration_number' => ['nullable','string','max:100'],
            'photo' => ['nullable','image','max:5120'],
            'aadhaar_card' => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'ration_card' => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'panchayath_letter' => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'disability_card' => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'doctor_certificate' => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'vincent_depaul_card' => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'doc_names.*' => ['nullable','string','max:255'],
            'doc_files.*' => ['nullable','file','max:8192'],
            'location_id' => ['nullable','exists:locations,id'],
            'admitted_by' => ['nullable','exists:users,id'],
            'verified_by' => ['nullable','exists:users,id'],
        ];

        if ($this->input('type') === 'child') {
            $rules['father_name'][] = 'required_without:guardian_name';
            $rules['mother_name'][] = 'required_without:guardian_name';
        }
        if (in_array($this->input('type'), ['elderly','rehabilitation'])) {
            $rules['marital_status'][0] = 'required';
        }

        return $rules;
    }
}
