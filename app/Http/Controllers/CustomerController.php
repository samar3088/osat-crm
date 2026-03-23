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
        try {
            $customers = $this->service->getAll($request->search ?? '');
            return response()->json(['success' => true, 'data' => $customers]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
}