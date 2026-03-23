<?php

namespace App\Http\Controllers;

use App\Services\ConveyanceService;
use App\Models\Conveyance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConveyancesExport;

class ConveyanceController extends Controller
{
    public function __construct(
        private ConveyanceService $service
    ) {}

    /**
     * Show conveyance page
     */
    public function index(): View
    {
        return view('conveyance.index');
    }

    /**
     * AJAX — Get all conveyances
     */
    public function list(Request $request): JsonResponse
    {
        $user  = auth()->user();
        $query = Conveyance::with([
            'user:id,name',
            'actionedBy:id,name',
        ])
        ->select([
            'conveyances.id', 'conveyances.user_id',
            'conveyances.conveyance_type', 'conveyances.conveyance_date',
            'conveyances.amount', 'conveyances.remarks',
            'conveyances.bill_path', 'conveyances.status',
            'conveyances.action_remarks', 'conveyances.actioned_by',
            'conveyances.actioned_at', 'conveyances.created_at',
        ]);

        if (!$user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('team_member', fn($c) => e($c->user->name ?? '—'))
            ->addColumn('type_badge', function($c) {
                $colors = [
                    'Travel'        => 'bg-blue-50 text-blue-600',
                    'Food'          => 'bg-green-50 text-green-600',
                    'Accommodation' => 'bg-purple-50 text-purple-600',
                    'Fuel'          => 'bg-orange-50 text-orange-500',
                    'Other'         => 'bg-gray-50 text-gray-500',
                ];
                $cls = $colors[$c->conveyance_type] ?? 'bg-gray-50 text-gray-500';
                return "<span class=\"px-2 py-1 rounded-full text-xs font-bold {$cls}\">" . e($c->conveyance_type) . "</span>";
            })
            ->addColumn('conveyance_date', fn($c) => $c->conveyance_date->format('d M Y'))
            ->addColumn('amount_fmt', fn($c) => '₹' . number_format($c->amount, 2))
            ->addColumn('bill_link', function($c) {
                return $c->bill_path
                    ? '<a href="' . asset('storage/' . $c->bill_path) . '" target="_blank"
                        class="flex items-center gap-1 text-xs text-primary font-bold hover:underline">
                        <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>View</a>'
                    : '<span class="text-xs text-crm-gray">No bill</span>';
            })
            ->addColumn('status_badge', function($c) {
                $config = [
                    'pending'  => 'bg-orange-50 text-orange-500',
                    'approved' => 'bg-green-50 text-green-600',
                    'rejected' => 'bg-red-50 text-red-500',
                ];
                $cls   = $config[$c->status] ?? 'bg-gray-50 text-gray-500';
                $label = ucfirst($c->status);
                return "<span class=\"px-3 py-1 rounded-full text-xs font-bold {$cls}\">{$label}</span>";
            })
            ->addColumn('actions', function($c) use ($user) {
                $actions = '';
                if ($user->isSuperAdmin() && $c->status === 'pending') {
                    $actions .= '
                        <button onclick="openActionModal(' . $c->id . ',\'approve\',\'' . e($c->user->name ?? '') . '\',\'' . e($c->conveyance_type) . '\',\'' . number_format($c->amount, 2) . '\')"
                                class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center hover:bg-green-500 hover:text-white text-green-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                        <button onclick="openActionModal(' . $c->id . ',\'reject\',\'' . e($c->user->name ?? '') . '\',\'' . e($c->conveyance_type) . '\',\'' . number_format($c->amount, 2) . '\')"
                                class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center hover:bg-red-500 hover:text-white text-red-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>';
                }
                if (!$user->isSuperAdmin() && $c->status === 'pending') {
                    $actions .= '
                        <button onclick="openDeleteModal(' . $c->id . ')"
                                class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center hover:bg-red-500 hover:text-white text-red-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6"/><path d="M14 11v6"/>
                            </svg>
                        </button>';
                }
                if (!$actions) $actions = '<span class="text-xs text-crm-gray">—</span>';
                return '<div class="flex items-center gap-2">' . $actions . '</div>';
            })
            ->filter(function($query) use ($request) {
                if ($request->filter_status) {
                    $query->where('conveyances.status', $request->filter_status);
                }
                if ($request->filter_type) {
                    $query->where('conveyances.conveyance_type', $request->filter_type);
                }
                if ($request->filter_member) {
                    $query->whereHas('user', fn($q) =>
                        $q->where('name', 'like', "%{$request->filter_member}%")
                    );
                }
            }, true)
            ->rawColumns(['type_badge', 'bill_link', 'status_badge', 'actions'])
            ->make(true);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new ConveyancesExport(auth()->user(), $request->all()),
            'conveyances-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * AJAX — Submit conveyance claim
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'conveyance_type' => ['required', 'string'],
            'conveyance_date' => ['required', 'date', 'before_or_equal:today'],
            'amount'          => ['required', 'numeric', 'min:1'],
            'remarks'         => ['nullable', 'string', 'max:500'],
            'bill'            => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'conveyance_type.required' => 'Please select a conveyance type.',
            'conveyance_date.required' => 'Please select the expense date.',
            'conveyance_date.before_or_equal' => 'Date cannot be in the future.',
            'amount.required'          => 'Amount is required.',
            'amount.min'               => 'Amount must be at least ₹1.',
            'bill.mimes'               => 'Bill must be JPG, PNG or PDF.',
            'bill.max'                 => 'Bill size cannot exceed 2MB.',
        ]);

        try {
            $conveyance = $this->service->create(
                $request->all(),
                $request->file('bill')
            );
            return response()->json([
                'success' => true,
                'message' => "Conveyance claim submitted successfully.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Approve conveyance
     */
    public function approve(Request $request, Conveyance $conveyance): JsonResponse
    {
        $request->validate([
            'action_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->service->approve($conveyance, $request->action_remarks ?? '');
            return response()->json([
                'success' => true,
                'message' => 'Conveyance approved successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Reject conveyance
     */
    public function reject(Request $request, Conveyance $conveyance): JsonResponse
    {
        $request->validate([
            'action_remarks' => ['required', 'string', 'max:500'],
        ], [
            'action_remarks.required' => 'Please provide a reason for rejection.',
        ]);

        try {
            $this->service->reject($conveyance, $request->action_remarks);
            return response()->json([
                'success' => true,
                'message' => 'Conveyance rejected.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Delete conveyance (only pending, own claims)
     */
    public function destroy(Conveyance $conveyance): JsonResponse
    {
        // Only allow delete if pending and own claim
        if ($conveyance->status !== 'pending' || $conveyance->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own pending claims.',
            ], 403);
        }

        try {
            $this->service->delete($conveyance);
            return response()->json([
                'success' => true,
                'message' => 'Conveyance claim deleted.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'success'  => true,
            'pending'  => Conveyance::where('status', 'pending')->count(),
            'approved' => Conveyance::where('status', 'approved')->count(),
            'rejected' => Conveyance::where('status', 'rejected')->count(),
        ]);
    }
}