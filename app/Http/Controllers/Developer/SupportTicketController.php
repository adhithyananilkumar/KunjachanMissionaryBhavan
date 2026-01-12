<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewTicketReply;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $q = SupportTicket::with('user')->latest('last_activity_at');
        if($request->filled('status')) $q->where('status',$request->status);
        if($request->filled('search')) $q->where('title','like','%'.$request->search.'%');
        $tickets = $q->paginate(25)->withQueryString();
        return view('developer.tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['replies.user']);
        return view('developer.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        // Do not allow replying or changing status on closed tickets
        if ($ticket->status === 'closed') {
            return back()->with('error', 'Ticket is closed. No further changes allowed.');
        }
    $request->validate(['message'=>'required|string','attachment'=>'nullable|file|max:5120','status'=>'nullable|string|max:50']);
    $attach=null; if($request->hasFile('attachment')) { $file=$request->file('attachment'); $dir=\App\Support\StoragePath::ticketReplyDir($ticket->id); $name=\App\Support\StoragePath::uniqueName($file); $attach=\Storage::putFileAs($dir,$file,$name); }
        $reply = TicketReply::create([
            'support_ticket_id'=>$ticket->id,
            'user_id'=>Auth::id(),
            'message'=>$request->message,
            'attachment_path'=>$attach
        ]);
        if($request->filled('status')) $ticket->status=$request->status;
        $ticket->last_activity_at=now();
        $ticket->save();
        // notify ticket owner
        $ticket->user->notify(new NewTicketReply($ticket,$reply));
        return back();
    }
}
