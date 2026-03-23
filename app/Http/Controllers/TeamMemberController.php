<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamMemberRequest;
use App\Services\TeamMemberService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Validation\ValidationException;

class TeamMemberController extends Controller
{
    public function __construct(
        private TeamMemberService $service
    ) {}

    /**
     * Show team members list page
     */
    public function index(): View
    {
        $admins = $this->service->getAdmins();
        return view('team-members.index', compact('admins'));
    }

    /**
     * AJAX — Get all team members
     */
    public function list(Request $request): JsonResponse
    {
        $query = User::role('team_member')
        ->with(['assignedTo:id,name'])
        ->withCount('clients')
        ->select([
            'users.id', 'users.name', 'users.email',
            'users.employee_code', 'users.work_type',
            'users.assigned_to', 'users.is_active', 'users.created_at',
        ]);

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('name_email', function($m) {
                return '
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-primary">' . strtoupper(substr($m->name, 0, 1)) . '</span>
                        </div>
                        <div>
                            <div class="font-bold text-dark text-sm">' . e($m->name) . '</div>
                            <div class="text-xs text-gray-400">' . e($m->email) . '</div>
                        </div>
                    </div>';
            })
            ->addColumn('status_badge', function($m) {
                $cls = $m->is_active
                    ? 'bg-green-50 text-green-600 hover:bg-green-100'
                    : 'bg-red-50 text-red-500 hover:bg-red-100';
                $label = $m->is_active ? 'Active' : 'Inactive';
                return "<button onclick=\"toggleStatus({$m->id}, this)\"
                                class=\"px-3 py-1 rounded-full text-xs font-bold transition-all {$cls}\">
                            {$label}
                        </button>";
            })
            ->addColumn('created_at', fn($m) => $m->created_at->format('d M Y'))
            ->addColumn('assigned_name', fn($m) => $m->assignedTo?->name ?? '—')
            ->addColumn('clients_count', fn($m) => $m->clients_count ?? 0)
            ->addColumn('actions', function($m) {
                return '
                    <div class="flex items-center gap-2">
                        <button onclick="openEditModal(' . $m->id . ')" title="Edit"
                                class="w-8 h-8 rounded-lg bg-primary-light flex items-center justify-center
                                    hover:bg-primary hover:text-white text-primary transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button onclick="openTargetModal(' . $m->id . ', \'' . e($m->name) . '\')" title="Set Target"
                                class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center
                                    hover:bg-green-500 hover:text-white text-green-500 transition-all">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <circle cx="12" cy="12" r="6"/>
                                <circle cx="12" cy="12" r="2"/>
                            </svg>
                        </button>
                        <button onclick="openDeleteModal(' . $m->id . ', \'' . e($m->name) . '\')" title="Delete"
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
                    $q->where('users.name', 'like', "%{$keyword}%")
                    ->orWhere('users.email', 'like', "%{$keyword}%")
                    ->orWhere('users.employee_code', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('assigned_name', function($query, $keyword) {
                $query->whereHas('assignedTo', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('status_badge', function($query, $keyword) {
                $active = strtolower($keyword) === 'active' ? 1 : 0;
                $query->where('users.is_active', $active);
            })
            ->rawColumns(['name_email', 'status_badge', 'actions'])
            ->filter(function($query) use ($request) {
                if ($request->filter_status) {
                    $active = $request->filter_status === 'Active' ? 1 : 0;
                    $query->where('users.is_active', $active);
                }
                if ($request->filter_worktype) {
                    $query->where('users.work_type', $request->filter_worktype);
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
     * AJAX — Create team member
     */
    public function store(TeamMemberRequest $request): JsonResponse
    {
        try {
            $member = $this->service->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => "Team member {$member->name} created successfully.",
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Get single team member for edit
     */
    public function show(User $teamMember): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $teamMember->id,
                'name'          => $teamMember->name,
                'email'         => $teamMember->email,
                'employee_code' => $teamMember->employee_code,
                'work_type'     => $teamMember->work_type,
                'assigned_to'   => $teamMember->assigned_to,
                'is_active'     => $teamMember->is_active,
            ],
        ]);
    }

    /**
     * AJAX — Update team member
     */
    public function update(TeamMemberRequest $request, User $teamMember): JsonResponse
    {
        try {
            $member = $this->service->update($teamMember, $request->validated());
            return response()->json([
                'success' => true,
                'message' => "Team member {$member->name} updated successfully.",
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Toggle status
     */
    public function toggleStatus(User $teamMember): JsonResponse
    {
        try {
            $member = $this->service->toggleStatus($teamMember);
            return response()->json([
                'success' => true,
                'message' => "{$member->name} is now " . ($member->is_active ? 'Active' : 'Inactive'),
                'is_active' => $member->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Delete team member
     */
    public function destroy(User $teamMember): JsonResponse
    {
        try {
            $name = $teamMember->name;
            $this->service->delete($teamMember);
            return response()->json([
                'success' => true,
                'message' => "{$name} has been removed.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Set target
     */
    public function setTarget(Request $request, User $teamMember): JsonResponse
    {
        $request->validate([
            'year'           => ['required', 'integer'],
            'month'          => ['required', 'integer', 'min:1', 'max:12'],
            'type'           => ['required', 'string', 'in:SIP,Lumpsum'],
            'target_amount'  => ['required', 'numeric', 'min:0'],
            'target_investors'=> ['nullable', 'integer', 'min:0'],
            'plan'           => ['nullable', 'string'],
            'category'       => ['nullable', 'string'],
        ]);

        try {
            $this->service->setTarget($teamMember, $request->all());
            return response()->json([
                'success' => true,
                'message' => "Target set successfully for {$teamMember->name}.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Download sample target Excel
     */
    public function downloadSampleTarget(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SampleTargetExport(),
            'sample-target-upload.xlsx'
        );
    }

    /**
     * Upload target Excel
     */
    public function uploadTarget(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv', 'max:2048'],
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\TargetImport(),
                $request->file('file')
            );

            return response()->json([
                'success' => true,
                'message' => 'Targets uploaded successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate unique employee code
     */
    public function generateCode(): JsonResponse
    {
        $last = User::whereNotNull('employee_code')
            ->where('employee_code', 'like', 'EMP-%')
            ->orderByDesc('id')
            ->value('employee_code');

        // Extract number from last code e.g. EMP-01 → 1
        $lastNum = 0;
        if ($last) {
            $parts   = explode('-', $last);
            $lastNum = (int) end($parts);
        }

        // Generate next code with 2 digit padding
        do {
            $lastNum++;
            $newCode = 'EMP-' . str_pad($lastNum, 3, '0', STR_PAD_LEFT);
        } while (User::where('employee_code', $newCode)->exists());

        return response()->json(['success' => true, 'code' => $newCode]);
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TeamMembersExport($request->all()),
            'team-members-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}