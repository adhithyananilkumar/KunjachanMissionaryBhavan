<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\CaseLogEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaseLogEntryController extends Controller
{
    public function store(Request $request, Inmate $inmate)
    {
        abort_unless($inmate->institution_id === Auth::user()->institution_id, 403);
        $data = $request->validate([
            'entry_date' => 'required|date',
            'entry_text' => 'required|string'
        ]);
        $data['inmate_id'] = $inmate->id;
        $data['user_id'] = Auth::id();
        CaseLogEntry::create($data);
        return back()->with('success','Case log entry added.');
    }
}
