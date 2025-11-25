<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Notifications\NewTicketReply;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())->latest('last_activity_at')->paginate(15);
        return view('tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 403);
        $ticket->load(['replies.user']);
        return view('tickets.show', compact('ticket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'required|string',
            'screenshot'=>'nullable|image|max:4096'
        ]);
        $path=null;
        // Create the ticket first to obtain an ID, then upload under that folder
        $ticket = SupportTicket::create([
            'user_id'=>Auth::id(),
            'title'=>$request->title,
            'description'=>$request->description,
            'status'=>'open',
            'last_activity_at' => now(),
        ]);
        if($request->hasFile('screenshot')){
            $file = $request->file('screenshot');
            $dir = \App\Support\StoragePath::ticketScreenshotDir($ticket->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $path = \Storage::putFileAs($dir, $file, $name);
            $ticket->update(['screenshot_path'=>$path]);
        }
        return redirect()->route('tickets.show',$ticket)->with('status','Ticket created');
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        // Prevent any reply if ticket is closed
        if ($ticket->status === 'closed') {
            abort(403, 'Ticket is closed. No further replies allowed.');
        }
        abort_unless($ticket->user_id === Auth::id(), 403);
    $request->validate(['message'=>'required|string','attachment'=>'nullable|file|max:5120']);
    $attach=null; if($request->hasFile('attachment')){ $file=$request->file('attachment'); $dir=\App\Support\StoragePath::ticketReplyDir($ticket->id); $name=\App\Support\StoragePath::uniqueName($file); $attach=\Storage::putFileAs($dir,$file,$name); }
        $reply = TicketReply::create([
            'support_ticket_id'=>$ticket->id,
            'user_id'=>Auth::id(),
            'message'=>$request->message,
            'attachment_path'=>$attach
        ]);
        $ticket->update(['last_activity_at'=>now()]);
        // notify developer(s) - naive: all developers
        foreach(\App\Models\User::where('role','developer')->get() as $dev){
            $dev->notify(new NewTicketReply($ticket,$reply));
        }
        return back();
    }
}
