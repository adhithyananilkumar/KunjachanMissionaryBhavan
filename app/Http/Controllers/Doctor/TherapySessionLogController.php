<?php
namespace App\Http\Controllers\Doctor;
use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\TherapySessionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TherapySessionLogController extends Controller
{
    public function store(Request $request, Inmate $inmate)
    {
        abort_unless($inmate->institution_id === Auth::user()->institution_id, 403);
        $data = $request->validate([
            'session_date' => 'required|date',
            'session_notes' => 'required|string'
        ]);
        $data['inmate_id'] = $inmate->id;
        $data['doctor_id'] = Auth::id();
        TherapySessionLog::create($data);
        return back()->with('success','Therapy session logged.');
    }
}
