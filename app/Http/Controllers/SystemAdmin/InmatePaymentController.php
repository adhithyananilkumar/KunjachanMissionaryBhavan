<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\InmatePayment;
use App\Models\Institution;
<<<<<<< HEAD
use App\Services\Pdf\PdfManager;
use Carbon\Carbon;
=======
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
use Illuminate\Http\Request;

class InmatePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = InmatePayment::with(['inmate.institution','institution']);

        $institutionId = $request->get('institution_id');
        $status = $request->get('status');
        $period = trim((string)$request->get('period',''));
        $search = trim((string)$request->get('search',''));
        $inmateId = $request->get('inmate_id');

        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }
        if ($inmateId) {
            $query->where('inmate_id', $inmateId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($period !== '') {
            $query->where('period_label', 'like', "%{$period}%");
        }
        if ($search !== '') {
            $query->whereHas('inmate', function($q) use ($search) {
                $q->where('first_name','like',"%{$search}%")
                  ->orWhere('last_name','like',"%{$search}%")
                  ->orWhereRaw("CONCAT(first_name,' ',COALESCE(last_name,'')) like ?", ["%{$search}%"])
                  ->orWhere('admission_number','like',"%{$search}%")
                  ->orWhere('registration_number','like',"%{$search}%");
            });
        }

        $payments = $query->orderBy('payment_date','desc')->orderBy('id','desc')
            ->paginate(20)->appends($request->only('institution_id','status','period','search','inmate_id'));

        $institutions = Institution::orderBy('name')->get(['id','name']);
        $statuses = ['pending','paid','failed','refunded'];

        $inmatesForSelect = Inmate::orderBy('first_name')->orderBy('id')
            ->get(['id','first_name','last_name','admission_number']);

<<<<<<< HEAD
        $basePaid = (clone $query)->where('status', 'paid');

        $now = Carbon::now();
        $monthPaid = (clone $basePaid)
            ->whereBetween('payment_date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);

        $summary = [
            'total_amount' => $monthPaid->sum('amount'),
            'count' => (clone $monthPaid)->count(),
            'all_time_total' => $basePaid->sum('amount'),
            'all_time_count' => (clone $query)->count(),
=======
        $summary = [
            'total_amount' => (clone $query)->where('status','paid')->sum('amount'),
            'count' => (clone $query)->count(),
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
        ];

        return view('system_admin.payments.index', compact(
            'payments',
            'institutions',
            'institutionId',
            'statuses',
            'status',
            'period',
            'search',
            'summary',
            'inmateId',
            'inmatesForSelect'
        ));
    }

    public function storeForInmate(Request $request, Inmate $inmate)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'payment_date' => 'required|date',
            'period_label' => 'nullable|string|max:100',
            'status' => 'required|in:pending,paid,failed,refunded',
            'method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['currency'] = strtoupper($data['currency'] ?? 'INR');
        $data['inmate_id'] = $inmate->id;
        $data['institution_id'] = $inmate->institution_id;

        InmatePayment::create($data);

        return redirect()
            ->route('system_admin.inmates.show', $inmate)
            ->with('success','Payment recorded successfully.');
    }
<<<<<<< HEAD

    public function downloadReceipt(InmatePayment $payment, PdfManager $pdf)
    {
        $payment->loadMissing(['inmate.institution', 'institution']);

        return $pdf->downloadTemplate('payment_receipt', [
            'payment' => $payment,
        ]);
    }

    public function downloadReport(Request $request, PdfManager $pdf)
    {
        $data = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'method' => ['nullable', 'string', 'max:50'],
            'mode' => ['nullable', 'in:summary,detailed'],
        ]);

        $mode = $data['mode'] ?? 'detailed';

        $query = InmatePayment::with(['inmate.institution', 'institution'])
            ->where('status', 'paid');

        if (! empty($data['from_date'])) {
            $query->whereDate('payment_date', '>=', $data['from_date']);
        }
        if (! empty($data['to_date'])) {
            $query->whereDate('payment_date', '<=', $data['to_date']);
        }
        if (! empty($data['method']) && $data['method'] !== 'all') {
            $query->where('method', $data['method']);
        }

        $payments = $query->orderBy('payment_date')->orderBy('id')->get();

        $summary = [
            'total_amount' => $payments->sum('amount'),
            'count' => $payments->count(),
            'from_date' => ! empty($data['from_date']) ? Carbon::parse($data['from_date']) : null,
            'to_date' => ! empty($data['to_date']) ? Carbon::parse($data['to_date']) : null,
            'method' => $data['method'] ?? 'all',
        ];

        return $pdf->downloadTemplate('payments_report', [
            'payments' => $payments,
            'summary' => $summary,
            'mode' => $mode,
        ]);
    }
=======
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
}
