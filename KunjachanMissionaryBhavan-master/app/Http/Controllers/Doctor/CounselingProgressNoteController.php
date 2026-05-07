<?php
namespace App\Http\Controllers\Doctor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Inmate,CounselingProgressNote};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CounselingProgressNoteController extends Controller
{
    public function store(Request $request, Inmate $inmate)
    {
        $this->authorize('update', $inmate); // optional policy if exists
        $data = $request->validate([
            'note_date' => ['required','date'],
            'progress_assessment' => ['required','string'],
            'milestones_achieved' => ['nullable','string']
        ]);
        CounselingProgressNote::create([
            'inmate_id' => $inmate->id,
            'user_id' => Auth::id(),
            'note_date' => Carbon::parse($data['note_date']),
            'progress_assessment' => $data['progress_assessment'],
            'milestones_achieved' => $data['milestones_achieved'] ?? null,
        ]);
        return back()->with('success','Counseling progress note added.');
    }
}
