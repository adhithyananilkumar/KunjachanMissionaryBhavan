<?php
namespace App\Http\Controllers\Nurse;
use App\Http\Controllers\Controller; use App\Models\Inmate;
class InmateController extends Controller
{
    public function index(){ $inmates=Inmate::where('institution_id', auth()->user()->institution_id)->paginate(15); return view('nurse.inmates.index', compact('inmates')); }
    public function show(Inmate $inmate){ abort_unless($inmate->institution_id === auth()->user()->institution_id, 403); return view('nurse.inmates.show', compact('inmate')); }
}
