<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Inmate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeriatricCarePlanController extends Controller
{
    public function storeOrUpdate(Request $request, Inmate $inmate)
    {
        abort_unless($inmate->institution_id === Auth::user()->institution_id, 403);
        $data = $request->validate([
            'mobility_status' => 'nullable|string|max:100',
            'dietary_needs' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:100',
            'emergency_relationship' => 'nullable|string|max:100',
        ]);
        $contact = [
            'name' => $data['emergency_name'] ?? null,
            'phone' => $data['emergency_phone'] ?? null,
            'relationship' => $data['emergency_relationship'] ?? null,
        ];
        unset($data['emergency_name'],$data['emergency_phone'],$data['emergency_relationship']);
        $payload = array_merge($data,['emergency_contact_details'=>$contact]);
        $plan = $inmate->geriatricCarePlan;
        if($plan){ $plan->update($payload); } else { $inmate->geriatricCarePlan()->create($payload); }
        return back()->with('success','Geriatric care plan saved.');
    }
}
