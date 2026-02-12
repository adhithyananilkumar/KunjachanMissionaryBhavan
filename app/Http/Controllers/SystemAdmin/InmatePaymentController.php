<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use App\Models\InmatePayment;
use App\Models\Institution;
use App\Services\Pdf\PdfManager;
use Carbon\Carbon;
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
        $method = trim((string)$request->get('method',''));
        $reportMode = $request->get('report_mode', 'detailed');

        $dateMode = $request->get('date_mode', 'all');
        $month = trim((string)$request->get('month', ''));
        $fromDate = trim((string)$request->get('from_date', ''));
        $toDate = trim((string)$request->get('to_date', ''));

        // Default view: show current month by default (elder-friendly)
        $hasAnyFilters = $request->filled('institution_id')
            || $request->filled('inmate_id')
            || $request->filled('status')
            || $request->filled('period')
            || $request->filled('search')
            || $request->filled('date_mode')
            || $request->filled('month')
            || $request->filled('from_date')
            || $request->filled('to_date')
            || $request->filled('method');

        if (! $hasAnyFilters) {
            $dateMode = 'month';
            $month = Carbon::now()->format('Y-m');
        }

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
        if ($method !== '') {
            $query->where('method', $method);
        }

        if ($dateMode === 'month' && $month !== '') {
            try {
                $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $end = (clone $start)->endOfMonth();
                $query->whereBetween('payment_date', [$start, $end]);
            } catch (\Throwable $e) {
                // ignore invalid month
            }
        } elseif ($dateMode === 'range') {
            if ($fromDate !== '') {
                $query->whereDate('payment_date', '>=', $fromDate);
            }
            if ($toDate !== '') {
                $query->whereDate('payment_date', '<=', $toDate);
            }
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
            ->paginate(20)->appends($request->only('institution_id','status','period','search','inmate_id','date_mode','month','from_date','to_date','method'));

        $institutions = Institution::orderBy('name')->get(['id','name']);
        $statuses = ['pending','paid','failed','refunded'];
        $methods = ['cash','upi','bank_transfer','card','other'];

        $inmatesForSelect = Inmate::orderBy('first_name')->orderBy('id')
            ->get(['id','first_name','last_name','admission_number']);

        $paidQuery = (clone $query)->where('status', 'paid');
        $summary = [
            'paid_total' => $paidQuery->sum('amount'),
            'paid_count' => (clone $paidQuery)->count(),
            'total_count' => (clone $query)->count(),
        ];

        $filters = [
            'institution_id' => $institutionId,
            'status' => $status,
            'search' => $search,
            'date_mode' => $dateMode,
            'month' => $month,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ];

        if ($request->ajax()) {
            return view('system_admin.payments._results', [
                'payments' => $payments,
                'summary' => $summary,
                'filters' => array_filter($filters, fn($v) => trim((string)$v) !== ''),
            ]);
        }

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
            'inmatesForSelect',
            'dateMode',
            'month',
            'fromDate',
            'toDate',
            'method',
            'methods',
            'filters',
            'reportMode'
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
            'institution_id' => ['nullable', 'integer'],
            'inmate_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:20'],
            'search' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'string', 'max:100'],
            'date_mode' => ['nullable', 'in:all,month,range'],
            'month' => ['nullable', 'date_format:Y-m'],
            'mode' => ['nullable', 'in:summary,detailed'],
        ]);

        $mode = $data['mode'] ?? 'detailed';

        $query = InmatePayment::with(['inmate.institution', 'institution']);

        $institutionName = null;
        if (!empty($data['institution_id'])) {
            $query->where('institution_id', $data['institution_id']);
            $institutionName = Institution::whereKey($data['institution_id'])->value('name');
        }
        if (!empty($data['inmate_id'])) {
            $query->where('inmate_id', $data['inmate_id']);
        }
        if (!empty($data['status']) && $data['status'] !== 'all') {
            $query->where('status', $data['status']);
        }

        $period = trim((string)($data['period'] ?? ''));
        if ($period !== '') {
            $query->where('period_label', 'like', "%{$period}%");
        }

        $search = trim((string)($data['search'] ?? ''));
        if ($search !== '') {
            $query->whereHas('inmate', function($q) use ($search) {
                $q->where('first_name','like',"%{$search}%")
                  ->orWhere('last_name','like',"%{$search}%")
                  ->orWhereRaw("CONCAT(first_name,' ',COALESCE(last_name,'')) like ?", ["%{$search}%"])
                  ->orWhere('admission_number','like',"%{$search}%")
                  ->orWhere('registration_number','like',"%{$search}%");
            });
        }

        if (! empty($data['from_date'])) {
            $query->whereDate('payment_date', '>=', $data['from_date']);
        }
        if (! empty($data['to_date'])) {
            $query->whereDate('payment_date', '<=', $data['to_date']);
        }

        $dateMode = $data['date_mode'] ?? 'all';
        if ($dateMode === 'month' && !empty($data['month'])) {
            $start = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth();
            $end = (clone $start)->endOfMonth();
            $query->whereBetween('payment_date', [$start, $end]);
        }

        if (! empty($data['method']) && $data['method'] !== 'all') {
            $query->where('method', $data['method']);
        }

        $payments = $query->orderBy('payment_date')->orderBy('id')->get();

        $paidTotal = $payments->where('status', 'paid')->sum('amount');
        $counts = $payments->groupBy('status')->map->count();

        $summary = [
            'paid_total_amount' => $paidTotal,
            'total_count' => $payments->count(),
            'paid_count' => (int)($counts['paid'] ?? 0),
            'pending_count' => (int)($counts['pending'] ?? 0),
            'failed_count' => (int)($counts['failed'] ?? 0),
            'refunded_count' => (int)($counts['refunded'] ?? 0),
            'from_date' => ! empty($data['from_date']) ? Carbon::parse($data['from_date']) : null,
            'to_date' => ! empty($data['to_date']) ? Carbon::parse($data['to_date']) : null,
            'method' => $data['method'] ?? 'all',
            'institution_name' => $institutionName,
            'status' => $data['status'] ?? 'all',
            'search' => $search !== '' ? $search : null,
        ];

        return $pdf->downloadTemplate('payments_report', [
            'payments' => $payments,
            'summary' => $summary,
            'mode' => $mode,
        ]);
    }
}
