<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\EducationalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationalRecordController extends Controller
{
    public function store(Request $request, Inmate $inmate)
    {
        abort_unless($inmate->institution_id === Auth::user()->institution_id, 403);
        $data = $request->validate([
            'school_name' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50',
            'academic_year' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'subjects.subject' => 'array',
            'subjects.grade' => 'array',
        ]);
        $data['inmate_id'] = $inmate->id;
        $subjects = [];
        $subs = $request->input('subjects.subject', []);
        $grades = $request->input('subjects.grade', []);
        foreach($subs as $idx=>$sub){
            $sub = trim($sub);
            if($sub==='') continue;
            $subjects[] = [
                'subject' => $sub,
                'grade' => $grades[$idx] ?? ''
            ];
        }
        $record = EducationalRecord::create($data);
        if($subjects){ $record->subjects_and_grades = $subjects; $record->save(); }
        return back()->with('success','Educational record added.');
    }
}
