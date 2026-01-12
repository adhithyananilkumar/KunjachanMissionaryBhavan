<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $allowedRoles = ['doctor','nurse','staff','admin'];
        $query = User::where('institution_id', Auth::user()->institution_id)
            ->whereIn('role',$allowedRoles);
        if($search = $request->get('search')){
            $query->where(function($q) use($search){
                $q->where('name','like',"%{$search}%")
                  ->orWhere('email','like',"%{$search}%");
            });
        }
        if($role = $request->get('role')){
            if(in_array($role, $allowedRoles, true)){
                $query->where('role',$role);
            }
        }
        $sort = $request->get('sort','name_asc');
        match($sort){
            'name_desc' => $query->orderBy('name','desc'),
            'created_asc' => $query->orderBy('id','asc'),
            'created_desc' => $query->orderBy('id','desc'),
            default => $query->orderBy('name','asc'),
        };
        $users = $query->paginate(15)->appends($request->only('search','role','sort'));
        $roles = $allowedRoles;
        return view('admin.staff.index', compact('users','roles','role','sort','search'));
    }

    public function create()
    {
        // Admin can create staff (not developer accounts) within their institution
        $roles = ['doctor','nurse','staff','admin'];
        return view('admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $roles = ['doctor','nurse','staff','admin'];
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:' . implode(',', $roles),
            'password' => 'required|string|min:8|confirmed',
        ]);
        $data['institution_id'] = Auth::user()->institution_id;
        $data['password'] = bcrypt($data['password']);
        User::create($data);
        return redirect()->route('admin.staff.index')->with('success','Staff member created successfully.');
    }

    public function edit(User $staff)
    {
        abort_unless($staff->institution_id === Auth::user()->institution_id, 403);
        $roles = ['doctor','nurse','staff','admin'];
        return view('admin.staff.edit', ['user' => $staff, 'roles' => $roles]);
    }

    public function update(Request $request, User $staff)
    {
        abort_unless($staff->institution_id === Auth::user()->institution_id, 403);
        $roles = ['doctor','nurse','staff','admin'];
        $data = $request->validate([
            'role' => 'required|in:' . implode(',', $roles),
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $staff->id,
            // password intentionally excluded for admin edits
        ]);
        $staff->update($data);
        return redirect()->route('admin.staff.index')->with('success','Staff member updated successfully.');
    }

    public function destroy(User $staff)
    {
        abort_unless($staff->institution_id === Auth::user()->institution_id, 403);
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success','Staff member deleted successfully.');
    }

    public function show(User $staff)
    {
        abort_unless($staff->institution_id === Auth::user()->institution_id, 403);
        // Build recent activity similar to System Admin view, scoped to admin routes
        $user = $staff; $activities = [];
        try {
            $medLogs = \App\Models\MedicationLog::with(['medicalRecord.inmate'])
                ->where('nurse_id', $user->id)
                ->latest('administration_time')->limit(10)->get();
            foreach($medLogs as $log){ $inmate = $log->medicalRecord?->inmate; $name = $inmate?->full_name ?? 'Inmate'; $activities[] = ['at'=>$log->administration_time,'icon'=>'capsule','text'=>"Administered medication to {$name}",'url'=>$inmate ? route('admin.inmates.show',$inmate) : null]; }
        } catch (\Throwable $e) {}
        try {
            $caseLogs = \App\Models\CaseLogEntry::with('inmate')->where('user_id',$user->id)->latest('entry_date')->limit(10)->get();
            foreach($caseLogs as $cl){ $inmate=$cl->inmate; $name=$inmate?->full_name ?? 'Inmate'; $activities[]=['at'=>$cl->entry_date,'icon'=>'journal-text','text'=>"Added case log for {$name}",'url'=>$inmate ? route('admin.inmates.show',$inmate).'#history' : null]; }
        } catch (\Throwable $e) {}
        try {
            $cNotes = \App\Models\CounselingProgressNote::with('inmate')->where('user_id',$user->id)->latest('note_date')->limit(10)->get();
            foreach($cNotes as $n){ $inmate=$n->inmate; $name=$inmate?->full_name ?? 'Inmate'; $activities[]=['at'=>$n->note_date,'icon'=>'chat-left-text','text'=>"Added counseling note for {$name}",'url'=>$inmate ? route('admin.inmates.show',$inmate).'#medical' : null]; }
        } catch (\Throwable $e) {}
        try {
            $tLogs = \App\Models\TherapySessionLog::with('inmate')->where('doctor_id',$user->id)->latest('session_date')->limit(10)->get();
            foreach($tLogs as $t){ $inmate=$t->inmate; $name=$inmate?->full_name ?? 'Inmate'; $activities[]=['at'=>$t->session_date,'icon'=>'heart-pulse','text'=>"Logged therapy session for {$name}",'url'=>$inmate ? route('admin.inmates.show',$inmate).'#medical' : null]; }
        } catch (\Throwable $e) {}
        try {
            $labOrdered = \App\Models\LabTest::with('inmate')->where('ordered_by',$user->id)->latest('ordered_date')->limit(10)->get();
            foreach($labOrdered as $lt){ $name=$lt->inmate?->full_name ?? 'Inmate'; $activities[]=['at'=>$lt->ordered_date,'icon'=>'beaker','text'=>"Ordered lab test {$lt->test_name} for {$name}",'url'=>$lt->inmate ? route('admin.inmates.show',$lt->inmate).'#medical' : null]; }
            $labUpdated = \App\Models\LabTest::with('inmate')->where('updated_by',$user->id)->latest('updated_at')->limit(10)->get();
            foreach($labUpdated as $lt){ $name=$lt->inmate?->full_name ?? 'Inmate'; $activities[]=['at'=>$lt->updated_at,'icon'=>'arrow-repeat','text'=>"Updated lab test {$lt->test_name} for {$name} ({$lt->status})",'url'=>$lt->inmate ? route('admin.inmates.show',$lt->inmate).'#medical' : null]; }
            $labReviewed = \App\Models\LabTest::with('inmate')->where('reviewed_by',$user->id)->latest('reviewed_at')->limit(10)->get();
            foreach($labReviewed as $lt){ $name=$lt->inmate?->full_name ?? 'Inmate'; $activities[]=['at'=>$lt->reviewed_at,'icon'=>'check2-circle','text'=>"Reviewed lab test {$lt->test_name} for {$name}",'url'=>$lt->inmate ? route('admin.inmates.show',$lt->inmate).'#medical' : null]; }
        } catch (\Throwable $e) {}
        try {
            $tickets = \App\Models\SupportTicket::where('user_id',$user->id)->latest('last_activity_at')->limit(10)->get();
            foreach($tickets as $t){ $activities[]=['at'=>$t->last_activity_at ?? $t->created_at,'icon'=>'life-preserver','text'=>"Support ticket: {$t->title}",'url'=>null]; }
            $replies = \App\Models\TicketReply::with('ticket')->where('user_id',$user->id)->latest('created_at')->limit(10)->get();
            foreach($replies as $r){ $title=$r->ticket?->title ?? ('#'.$r->support_ticket_id); $activities[]=['at'=>$r->created_at,'icon'=>'reply','text'=>"Replied on ticket: {$title}",'url'=>null]; }
        } catch (\Throwable $e) {}

        usort($activities, function($a,$b){ return ($b['at']?->timestamp ?? 0) <=> ($a['at']?->timestamp ?? 0); });
        $activities = array_slice($activities, 0, 12);
        return view('admin.staff.show', compact('user','activities'));
    }
}
