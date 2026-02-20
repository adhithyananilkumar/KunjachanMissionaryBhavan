<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\SupportTicketActivity;
use App\Notifications\NewTicketReply;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $showHistory = (bool) $request->boolean('history');

        $q = SupportTicket::where('user_id', Auth::id());
        if (!$showHistory) {
            $q->whereNull('archived_at')
                ->whereNotIn('status', [SupportTicket::STATUS_RESOLVED, SupportTicket::STATUS_CLOSED]);
        }

        $tickets = $q->latest('last_activity_at')->paginate(15)->withQueryString();
        return view('tickets.index', compact('tickets', 'showHistory'));
    }

    public function show(SupportTicket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 403);
        $ticket->forceFill(['user_last_seen_at' => now()])->save();
        $ticket->load(['replies.user']);
        return view('tickets.show', compact('ticket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'required|string',
            'module' => 'nullable|string|max:80',
            'severity' => 'nullable|string|max:20',
            'page_url' => 'nullable|string|max:2048',
            'screenshot'=>'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,heic,heif,pdf',
            'screenshots' => 'nullable|array|max:5',
            'screenshots.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,heic,heif,pdf',
        ]);

        $paths = [];
        // Create the ticket first to obtain an ID, then upload under that folder
        $ticket = SupportTicket::create([
            'user_id'=>Auth::id(),
            'title'=>$request->title,
            'description'=>$request->description,
            'module' => $request->input('module'),
            'severity' => $request->input('severity'),
            'page_url' => $request->input('page_url') ?: $request->headers->get('referer'),
            'app_version' => (string) (config('app.version') ?? ''),
            'deployment_tag' => (string) (config('app.deployment_tag') ?? ''),
            'environment' => [
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ],
            'status'=>SupportTicket::STATUS_OPEN,
            'last_activity_at' => now(),
        ]);

        SupportTicketActivity::create([
            'support_ticket_id' => $ticket->id,
            'actor_id' => Auth::id(),
            'type' => 'created',
            'meta' => ['severity' => $ticket->severity, 'module' => $ticket->module],
        ]);

        $files = [];
        if ($request->hasFile('screenshot')) {
            $files[] = $request->file('screenshot');
        }
        if ($request->hasFile('screenshots')) {
            foreach ((array) $request->file('screenshots') as $f) {
                if ($f) {
                    $files[] = $f;
                }
            }
        }

        foreach ($files as $file) {
            $dir = \App\Support\StoragePath::ticketScreenshotDir($ticket->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $paths[] = \Storage::putFileAs($dir, $file, $name);
        }

        if (!empty($paths)) {
            $ticket->forceFill([
                'screenshot_path' => $paths[0],
                'screenshot_paths' => $paths,
            ])->save();
        }

        return redirect()->route('tickets.show',$ticket)->with('status','Ticket created');
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 403);

        // Prevent replies once solved/resolved/closed (and also if archived)
        if (
            $ticket->status === SupportTicket::STATUS_RESOLVED
            || $ticket->status === SupportTicket::STATUS_CLOSED
            || $ticket->archived_at
        ) {
            return back()->with('error', 'This ticket is solved. Replies are disabled.');
        }

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,heic,heif,pdf',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,heic,heif,pdf',
        ]);

        $attach = null;
        $attachments = [];
        $files = [];
        if ($request->hasFile('attachment')) {
            $files[] = $request->file('attachment');
        }
        if ($request->hasFile('attachments')) {
            foreach ((array) $request->file('attachments') as $f) {
                if ($f) {
                    $files[] = $f;
                }
            }
        }
        foreach ($files as $idx => $file) {
            $dir = \App\Support\StoragePath::ticketReplyDir($ticket->id);
            $name = \App\Support\StoragePath::uniqueName($file);
            $p = \Storage::putFileAs($dir, $file, $name);
            $attachments[] = $p;
            if ($idx === 0) {
                $attach = $p;
            }
        }

        $reply = TicketReply::create([
            'support_ticket_id'=>$ticket->id,
            'user_id'=>Auth::id(),
            'message'=>$request->message,
            'attachment_path'=>$attach,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        $ticket->forceFill(['last_activity_at'=>now()])->save();

        SupportTicketActivity::create([
            'support_ticket_id' => $ticket->id,
            'actor_id' => Auth::id(),
            'type' => 'user_reply',
            'meta' => ['reply_id' => $reply->id],
        ]);

        // notify developer(s) - naive: all developers
        if ($ticket->assigned_to) {
            $dev = \App\Models\User::where('role', 'developer')->where('id', $ticket->assigned_to)->first();
            if ($dev) {
                $dev->notify(new NewTicketReply($ticket, $reply));
            }
        } else {
            foreach(\App\Models\User::where('role','developer')->get() as $dev){
                $dev->notify(new NewTicketReply($ticket,$reply));
            }
        }
        return back();
    }
}
