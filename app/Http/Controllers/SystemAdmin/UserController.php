<?php
namespace App\Http\Controllers\SystemAdmin; use App\Http\Controllers\Controller; use App\Models\User; use App\Models\Institution; use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index(Request $request){
        $query = User::query()->with('institution')->where('role','!=','developer');
        $institutionId = $request->get('institution_id');
        $role = $request->get('role');
        $sort = $request->get('sort','name_asc');
        if($institutionId){ $query->where('institution_id',$institutionId); }
        if($role){ $query->where('role',$role); }
        match($sort){
            'name_desc' => $query->orderBy('name','desc'),
            'created_asc' => $query->orderBy('id','asc'),
            'created_desc' => $query->orderBy('id','desc'),
            default => $query->orderBy('name','asc'),
        };
        $users = $query->paginate(15)->appends($request->only('institution_id','role','sort'));
        $institutions = Institution::orderBy('name')->get(['id','name']);
        $roles=['system_admin','admin','doctor','nurse','staff','guardian'];
        return view('system_admin.users.index', compact('users','institutions','institutionId','roles','role','sort'));
    }
    public function create(){ $institutions=Institution::orderBy('name')->get(); $roles=['system_admin','admin','doctor','nurse','staff','guardian']; return view('system_admin.users.create', compact('institutions','roles')); }
    public function store(Request $request){ $allowedRoles=['system_admin','admin','doctor','nurse','staff','guardian']; $validated=$request->validate(['name'=>'required|string|max:255','email'=>'required|email|unique:users,email','role'=>'required|string|in:'.implode(',',$allowedRoles),'password'=>'required|string|min:8|confirmed','institution_id'=>'nullable|exists:institutions,id']); if($validated['role']!=='system_admin'){ $validated['institution_id']=$validated['institution_id']??null; } else { $validated['institution_id']=null; } $validated['password']=bcrypt($validated['password']); User::create($validated); return redirect()->route('system_admin.users.index')->with('success','User created successfully!'); }
    public function edit(User $user){ if($user->role==='developer'){ return redirect()->route('system_admin.users.index')->with('error','Developer accounts cannot be managed.'); } $institutions=Institution::orderBy('name')->get(); $roles=['system_admin','admin','doctor','nurse','staff','guardian']; return view('system_admin.users.edit', compact('user','institutions','roles')); }
    public function show(User $user){
        if($user->role==='developer'){
            return redirect()->route('system_admin.users.index')->with('error','Developer accounts cannot be viewed.');
        }

        // Build a lightweight recent activity feed for this user across key entities
        $activities = [];

        // Medication administrations (nurse)
        try {
            $medLogs = \App\Models\MedicationLog::with(['medicalRecord.inmate'])
                ->where('nurse_id', $user->id)
                ->latest('administration_time')->limit(10)->get();
            foreach($medLogs as $log){
                $inmate = $log->medicalRecord?->inmate; $name = $inmate?->full_name ?? 'Inmate';
                $activities[] = [
                    'at' => $log->administration_time,
                    'icon' => 'capsule',
                    'text' => "Administered medication to {$name}",
                    'url' => $inmate ? route('system_admin.inmates.show',$inmate) : null,
                ];
            }
        } catch (\Throwable $e) { /* ignore */ }

        // Case log entries (admin)
        try {
            $caseLogs = \App\Models\CaseLogEntry::with('inmate')
                ->where('user_id',$user->id)->latest('entry_date')->limit(10)->get();
            foreach($caseLogs as $cl){
                $inmate=$cl->inmate; $name=$inmate?->full_name ?? 'Inmate';
                $activities[] = [
                    'at' => $cl->entry_date,
                    'icon' => 'journal-text',
                    'text' => "Added case log for {$name}",
                    'url' => $inmate ? route('system_admin.inmates.show',$inmate).'#history' : null,
                ];
            }
        } catch (\Throwable $e) { /* ignore */ }

        // Counseling notes (doctor/staff)
        try {
            $cNotes = \App\Models\CounselingProgressNote::with('inmate')
                ->where('user_id',$user->id)->latest('note_date')->limit(10)->get();
            foreach($cNotes as $n){
                $inmate=$n->inmate; $name=$inmate?->full_name ?? 'Inmate';
                $activities[] = [
                    'at' => $n->note_date,
                    'icon' => 'chat-left-text',
                    'text' => "Added counseling note for {$name}",
                    'url' => $inmate ? route('system_admin.inmates.show',$inmate).'#medical' : null,
                ];
            }
        } catch (\Throwable $e) { /* ignore */ }

        // Therapy sessions (doctor)
        try {
            $tLogs = \App\Models\TherapySessionLog::with('inmate')
                ->where('doctor_id',$user->id)->latest('session_date')->limit(10)->get();
            foreach($tLogs as $t){
                $inmate=$t->inmate; $name=$inmate?->full_name ?? 'Inmate';
                $activities[] = [
                    'at' => $t->session_date,
                    'icon' => 'heart-pulse',
                    'text' => "Logged therapy session for {$name}",
                    'url' => $inmate ? route('system_admin.inmates.show',$inmate).'#medical' : null,
                ];
            }
        } catch (\Throwable $e) { /* ignore */ }

        // Lab tests (doctor/nurse updates)
        try {
            $labOrdered = \App\Models\LabTest::with('inmate')->where('ordered_by',$user->id)->latest('ordered_date')->limit(10)->get();
            foreach($labOrdered as $lt){ $name=$lt->inmate?->full_name ?? 'Inmate'; $activities[]=[ 'at'=>$lt->ordered_date, 'icon'=>'beaker', 'text'=>"Ordered lab test {$lt->test_name} for {$name}", 'url'=>$lt->inmate ? route('system_admin.inmates.show',$lt->inmate).'#medical' : null ]; }
            $labUpdated = \App\Models\LabTest::with('inmate')->where('updated_by',$user->id)->latest('updated_at')->limit(10)->get();
            foreach($labUpdated as $lt){ $name=$lt->inmate?->full_name ?? 'Inmate'; $activities[]=[ 'at'=>$lt->updated_at, 'icon'=>'arrow-repeat', 'text'=>"Updated lab test {$lt->test_name} for {$name} ({$lt->status})", 'url'=>$lt->inmate ? route('system_admin.inmates.show',$lt->inmate).'#medical' : null ]; }
            $labReviewed = \App\Models\LabTest::with('inmate')->where('reviewed_by',$user->id)->latest('reviewed_at')->limit(10)->get();
            foreach($labReviewed as $lt){ $name=$lt->inmate?->full_name ?? 'Inmate'; $activities[]=[ 'at'=>$lt->reviewed_at, 'icon'=>'check2-circle', 'text'=>"Reviewed lab test {$lt->test_name} for {$name}", 'url'=>$lt->inmate ? route('system_admin.inmates.show',$lt->inmate).'#medical' : null ]; }
        } catch (\Throwable $e) { /* ignore */ }

        // Support tickets and replies (any role)
        try {
            $tickets = \App\Models\SupportTicket::where('user_id',$user->id)->latest('last_activity_at')->limit(10)->get();
            foreach($tickets as $t){
                $activities[]=[ 'at'=>$t->last_activity_at ?? $t->created_at, 'icon'=>'life-preserver', 'text'=>"Support ticket: {$t->title}", 'url'=>null ];
            }
            $replies = \App\Models\TicketReply::with('ticket')->where('user_id',$user->id)->latest('created_at')->limit(10)->get();
            foreach($replies as $r){ $title=$r->ticket?->title ?? ('#'.$r->support_ticket_id); $activities[]=[ 'at'=>$r->created_at, 'icon'=>'reply', 'text'=>"Replied on ticket: {$title}", 'url'=>null ]; }
        } catch (\Throwable $e) { /* ignore */ }

        // Sort all by date desc and keep most recent 12
        usort($activities, function($a,$b){ return ($b['at']?->timestamp ?? 0) <=> ($a['at']?->timestamp ?? 0); });
        $activities = array_slice($activities, 0, 12);

        return view('system_admin.users.show', compact('user','activities'));
    }
    public function update(Request $request, User $user){ if($user->role==='developer'){ return redirect()->route('system_admin.users.index')->with('error','Developer accounts cannot be modified.'); } $validated=$request->validate(['name'=>'required|string|max:255','email'=>'required|email|max:255|unique:users,email,'.$user->id,'role'=>'required|string|in:system_admin,admin,doctor,nurse,staff,guardian','institution_id'=>'nullable|exists:institutions,id']); $user->name=$validated['name']; $user->email=$validated['email']; $user->role=$validated['role']; $user->institution_id=$validated['role']==='system_admin'?null:($validated['institution_id']??null); if($request->filled('password')){ $request->validate(['password'=>'required|min:8|confirmed']); $user->password=bcrypt($request->password);} $user->save(); return redirect()->route('system_admin.users.index')->with('success','User updated successfully!'); }
    public function destroy(User $user){ if($user->role==='developer'){ return redirect()->route('system_admin.users.index')->with('error','Developer accounts cannot be deleted.'); } $user->delete(); return redirect()->route('system_admin.users.index')->with('success','User deleted successfully.'); }
    public function toggleBugReporting(User $user){ if($user->role==='developer'){ return response()->json(['ok'=>false,'message'=>'Not allowed'],403); } $user->can_report_bugs = !$user->can_report_bugs; $user->save(); return response()->json(['ok'=>true,'can_report_bugs'=>$user->can_report_bugs]); }
}
