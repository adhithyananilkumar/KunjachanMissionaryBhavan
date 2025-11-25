<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Examination;
use App\Models\Inmate;
use Illuminate\Http\Request;

class ExaminationController extends Controller
{
    public function store(Request $request, Inmate $inmate)
    {
        abort_unless($inmate->institution_id === auth()->user()->institution_id, 403);
        $data = $request->validate([
            'title' => ['nullable','string','max:255'],
            'notes' => ['required','string'],
            'severity' => ['nullable','in:mild,moderate,severe'],
            'observed_at' => ['nullable','date'],
        ]);
        $data['inmate_id'] = $inmate->id;
        $data['created_by'] = auth()->id();
        $data['creator_role'] = 'staff';
        Examination::create($data);
        return back()->with('status','Examination noted and sent to doctor.');
    }
}
