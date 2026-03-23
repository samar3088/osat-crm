<?php

namespace App\Http\Controllers;

use App\Services\ConveyanceService;
use App\Models\Conveyance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
    public function list(): JsonResponse
    {
        try {
            $data = $this->service->getAll();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
}