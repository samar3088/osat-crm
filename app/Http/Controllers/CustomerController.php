<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $service
    ) {}

    /**
     * Show customers list page
     */
    public function index(): View
    {
        $teamMembers = $this->service->getTeamMembers();
        return view('customers.index', compact('teamMembers'));
    }

    /**
     * AJAX — Get all customers
     */
    public function list(Request $request): JsonResponse
    {
        $user  = auth()->user();
        $query = Client::with(['assignedTo'])
            ->select('clients.*');

        // Data scoping
        if (!$user->isSuperAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('name_email', function($c) {
                return '
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-primary">' . strtoupper(substr($c->client_name, 0, 1)) . '</span>
                        </div>
                        <div>
                            <div class="font-bold text-dark text-sm">' . e($c->client_name) . '</div>
                            <div class="text-xs text-gray-400">' . e($c->client_email ?? '—') . '</div>
                        </div>
                    </div>';
            })
            ->addColumn('type_badge', function($c) {
                $colors = [
                    'New Client'      => 'bg-blue-50 text-blue-600',
                    'Existing Client' => 'bg-green-50 text-green-600',
                    'Prospect Client' => 'bg-orange-50 text-orange-500',
                ];
                $cls = $colors[$c->client_type] ?? 'bg-crm-light text-crm-gray';
                return "<span class=\"px-2 py-1 rounded-full text-xs font-bold {$cls}\">" . e($c->client_type ?? '—') . "</span>";
            })
            ->addColumn('status_badge', function($c) {
                $cls   = $c->is_active ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-red-50 text-red-500 hover:bg-red-100';
                $label = $c->is_active ? 'Active' : 'Inactive';
                return "<button onclick=\"toggleStatus({$c->id})\"
                                class=\"px-3 py-1 rounded-full text-xs font-bold transition-all {$cls}\">
                            {$label}
                        </button>";
            })
            ->addColumn('assigned_name', fn($c) => $c->assignedTo?->name ?? '—')
            ->addColumn('actions', function($c) {
                return '
                    <div class="flex items-center gap-2">
                        <a href="/customers/' . $c->id . '/profile"
                        class="w-8 h-8 rounded-lg bg-crm-light flex items-center justify-center
                                hover:bg-primary hover:text-white text-crm-gray transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                        <button onclick="openEditModal(' . $c->id . ')" title="Edit"
                                class="w-8 h-8 rounded-lg bg-primary-light flex items-center justify-center
                                    hover:bg-primary hover:text-white text-primary transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button onclick="openDeleteModal(' . $c->id . ', \'' . e($c->client_name) . '\')" title="Delete"
                                class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center
                                    hover:bg-red-500 hover:text-white text-red-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6"/><path d="M14 11v6"/>
                            </svg>
                        </button>
                    </div>';
            })
            ->filterColumn('name_email', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('clients.client_name',   'like', "%{$keyword}%")
                    ->orWhere('clients.client_email', 'like', "%{$keyword}%")
                    ->orWhere('clients.client_pan',   'like', "%{$keyword}%")
                    ->orWhere('clients.client_mobile','like', "%{$keyword}%");
                });
            })
            ->filterColumn('type_badge', function($query, $keyword) {
                $query->where('clients.client_type', 'like', "%{$keyword}%");
            })
            ->filterColumn('status_badge', function($query, $keyword) {
                $active = strtolower($keyword) === 'active' ? 1 : 0;
                $query->where('clients.is_active', $active);
            })
            ->filterColumn('assigned_name', function($query, $keyword) {
                $query->whereHas('assignedTo', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['name_email', 'type_badge', 'status_badge', 'actions'])
            ->filter(function($query) use ($request) {
                if ($request->filter_type) {
                    $query->where('clients.client_type', $request->filter_type);
                }
                if ($request->filter_status) {
                    $active = $request->filter_status === 'Active' ? 1 : 0;
                    $query->where('clients.is_active', $active);
                }
                if ($request->filter_assigned) {
                    $query->whereHas('assignedTo', function($q) use ($request) {
                        $q->where('name', 'like', "%{$request->filter_assigned}%");
                    });
                }
            }, true)
            ->make(true);
    }

    /**
     * AJAX — Create customer
     */
    public function store(Request $request): JsonResponse
    {
        $messages = [
            'client_name.required'  => 'Client name is required.',
            'client_mobile.min'     => 'Mobile number must be at least 10 digits.',
            'client_mobile.max'     => 'Mobile number cannot exceed 15 digits.',
            'client_mobile.regex'   => 'Please enter a valid mobile number.',
            'client_email.email'    => 'Please enter a valid email address.',
            'assigned_to.exists'    => 'Selected team member does not exist.',
        ];

        $request->validate([
            'client_name'   => ['required', 'string', 'max:150'],
            'client_pan'    => ['nullable', 'string', 'max:20'],
            'client_mobile' => [
                'nullable', 'string', 'min:10', 'max:15',
                'regex:/^[0-9+\-\s()]+$/',
            ],
            'client_email'  => ['nullable', 'email', 'max:150'],
            'client_type'   => ['nullable', 'string'],
            'assigned_to'   => ['nullable', 'exists:users,id'],
        ], $messages);

        try {
            $client = $this->service->create($request->all());
            return response()->json([
                'success' => true,
                'message' => "Client {$client->client_name} added successfully.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Get single customer for edit
     */
    public function show(Client $customer): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $customer->id,
                'client_name'   => $customer->client_name,
                'client_pan'    => $customer->client_pan,
                'client_mobile' => $customer->client_mobile,
                'client_email'  => $customer->client_email,
                'client_type'   => $customer->client_type,
                'source_detail' => $customer->source_detail,
                'date_of_birth' => $customer->date_of_birth?->format('Y-m-d'),
                'full_remarks'  => $customer->full_remarks,
                'assigned_to'   => $customer->assigned_to,
                'is_active'     => $customer->is_active,
            ],
        ]);
    }

    /**
     * AJAX — Update customer
     */
    public function update(Request $request, Client $customer): JsonResponse
    {
        $messages = [
            'client_name.required'   => 'Client name is required.',
            'client_mobile.min'      => 'Mobile number must be at least 10 digits.',
            'client_mobile.max'      => 'Mobile number cannot exceed 15 digits.',
            'client_mobile.regex'    => 'Please enter a valid mobile number.',
            'client_email.email'     => 'Please enter a valid email address.',
            'client_pan.max'         => 'PAN cannot exceed 20 characters.',
            'assigned_to.exists'     => 'Selected team member does not exist.',
        ];
        
        $request->validate([
            'client_name'   => ['required', 'string', 'max:150'],
            'client_pan'    => ['nullable', 'string', 'max:20'],
            'client_mobile' => [
                'nullable',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9+\-\s()]+$/', // allows +91, spaces, dashes
            ],
            'client_email'  => ['nullable', 'email', 'max:150'],
            'client_type'   => ['nullable', 'string'],
            'assigned_to'   => ['nullable', 'exists:users,id'],
        ]);

        try {
            $client = $this->service->update($customer, $request->all());
            return response()->json([
                'success' => true,
                'message' => "Client {$client->client_name} updated successfully.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Toggle status
     */
    public function toggleStatus(Client $customer): JsonResponse
    {
        try {
            $client = $this->service->toggleStatus($customer);
            return response()->json([
                'success'   => true,
                'message'   => "{$client->client_name} is now " . ($client->is_active ? 'Active' : 'Inactive'),
                'is_active' => $client->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Delete customer
     */
    public function destroy(Client $customer): JsonResponse
    {
        try {
            $name = $customer->client_name;
            $this->service->delete($customer);
            return response()->json([
                'success' => true,
                'message' => "{$name} has been removed.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * View customer profile
     */
    public function profile(Client $customer): View
    {
        return view('customers.profile', compact('customer'));
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CustomersExport($request->all()),
            'customers-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}