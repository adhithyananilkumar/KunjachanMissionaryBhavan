<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\SupportTicketActivity;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewTicketReply;
use App\Notifications\TicketStatusChanged;
use App\Notifications\TicketAssigned;

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
        $ticket->forceFill(['developer_last_seen_at' => now()])->save();
        $ticket->load(['replies.user']);
        return view('developer.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        // Do not allow replying or changing status on closed tickets
        if ($ticket->status === SupportTicket::STATUS_CLOSED) {
            return back()->with('error', 'Ticket is closed. No further changes allowed.');
        }

        $allowedStatuses = [
            SupportTicket::STATUS_OPEN,
            SupportTicket::STATUS_IN_PROGRESS,
            SupportTicket::STATUS_WAITING,
            SupportTicket::STATUS_RESOLVED,
        ];

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,heic,heif,pdf',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,heic,heif,pdf',
            'status' => 'nullable|string|in:' . implode(',', $allowedStatuses),
            'assigned_to' => 'nullable|integer|exists:users,id',
            'resolution_summary' => 'nullable|string|max:5000',
            'fixed_in_version' => 'nullable|string|max:50',
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

        SupportTicketActivity::create([
            'support_ticket_id' => $ticket->id,
            'actor_id' => Auth::id(),
            'type' => 'developer_reply',
            'meta' => ['reply_id' => $reply->id],
        ]);

        $oldStatus = $ticket->status;

        // Assignment (optional)
        if ($request->filled('assigned_to')) {
            $assignee = \App\Models\User::where('role', 'developer')->where('id', (int) $request->assigned_to)->first();
            if ($assignee) {
                $ticket->assigned_to = $assignee->id;
                $assignee->notify(new TicketAssigned($ticket, Auth::user()?->name));
                SupportTicketActivity::create([
                    'support_ticket_id' => $ticket->id,
                    'actor_id' => Auth::id(),
                    'type' => 'assigned',
                    'meta' => ['assigned_to' => $assignee->id],
                ]);
            }
        }

        // Status transitions
        if ($request->filled('status')) {
            $ticket->status = $request->status;
        }

        if ($ticket->status === SupportTicket::STATUS_RESOLVED && $oldStatus !== SupportTicket::STATUS_RESOLVED) {
            $ticket->resolved_at = now();
            $ticket->resolution_summary = $request->input('resolution_summary');
            $ticket->fixed_in_version = $request->input('fixed_in_version');
        }

        if ($ticket->status !== $oldStatus) {
            SupportTicketActivity::create([
                'support_ticket_id' => $ticket->id,
                'actor_id' => Auth::id(),
                'type' => 'status_changed',
                'meta' => ['from' => $oldStatus, 'to' => $ticket->status],
            ]);

            // notify ticket owner
            $ticket->user->notify(new TicketStatusChanged($ticket, (string) $oldStatus, (string) $ticket->status, Auth::user()?->name));
        }

        $ticket->last_activity_at = now();
        $ticket->save();
        // notify ticket owner
        $ticket->user->notify(new NewTicketReply($ticket,$reply));
        return back();
    }
}
