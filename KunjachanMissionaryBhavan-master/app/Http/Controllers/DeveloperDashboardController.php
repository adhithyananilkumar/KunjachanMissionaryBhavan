<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use App\Models\Inmate;
use App\Models\ActionRequest;
use App\Models\SupportTicket;

class DeveloperDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'institutions' => Institution::count(),
            'users' => User::count(),
            'inmates' => Inmate::count(),
            'pending_requests' => ActionRequest::where('status','pending')->count(),
            'open_bugs' => \Illuminate\Support\Facades\Schema::hasTable('support_tickets') ? SupportTicket::where('status','open')->count() : 0,
        ];

        // Inmates by type dataset (fallback label when null)
        $typeCounts = Inmate::selectRaw("COALESCE(NULLIF(type,''),'Unspecified') as type, COUNT(*) as total")
            ->groupBy('type')
            ->orderBy('total','desc')
            ->pluck('total','type');

        // Lightweight recent activity (since no audit log table yet): latest 10 items across entities
        $recent = collect();
        $recent = $recent->merge(Institution::latest('id')->take(5)->get()->map(fn($i)=>['when'=>$i->created_at,'type'=>'institution','label'=>$i->name]));
        $recent = $recent->merge(User::latest('id')->take(5)->get()->map(fn($u)=>['when'=>$u->created_at,'type'=>'user','label'=>$u->name]));
        $recent = $recent->merge(Inmate::latest('id')->take(5)->get()->map(fn($m)=>['when'=>$m->created_at,'type'=>'inmate','label'=>$m->full_name]));
        $recent = $recent->sortByDesc('when')->take(10)->values();

        return view('dashboards.developer', [
            'stats' => $stats,
            'typeCounts' => $typeCounts,
            'recent' => $recent,
        ]);
    }
}
