<?php
namespace App\Http\Controllers\SystemAdmin;
use App\Http\Controllers\Controller;
use App\Models\Institution; use App\Models\User; use App\Models\Inmate; use App\Models\ActionRequest; use App\Models\SupportTicket;
class SystemAdminDashboardController extends Controller
{
    public function index(){
        $stats = [
            'institutions' => Institution::count(),
            'users' => User::where('role','!=','developer')->count(),
            'inmates' => Inmate::count(),
            'pending_requests' => ActionRequest::where('status','pending')->count(),
            'open_bugs' => \Illuminate\Support\Facades\Schema::hasTable('support_tickets') ? SupportTicket::where('status','open')->count() : 0,
        ];

        $typeCounts = Inmate::selectRaw("COALESCE(NULLIF(type,''),'Unspecified') as type, COUNT(*) as total")
            ->groupBy('type')
            ->orderBy('total','desc')
            ->pluck('total','type');

        $recent = collect();
        $recent = $recent->merge(Institution::latest('id')->take(5)->get()->map(fn($i)=>['when'=>$i->created_at,'type'=>'institution','label'=>$i->name]));
        $recent = $recent->merge(User::where('role','!=','developer')->latest('id')->take(5)->get()->map(fn($u)=>['when'=>$u->created_at,'type'=>'user','label'=>$u->name]));
        $recent = $recent->merge(Inmate::latest('id')->take(5)->get()->map(fn($m)=>['when'=>$m->created_at,'type'=>'inmate','label'=>$m->full_name]));
        $recent = $recent->sortByDesc('when')->take(10)->values();

        return view('dashboards.system_admin', [
            'stats'=>$stats,
            'typeCounts'=>$typeCounts,
            'recent'=>$recent,
        ]);
    }
}
