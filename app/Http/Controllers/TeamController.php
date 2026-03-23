<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    public function __construct(
        private TeamService $service
    ) {}

    /**
     * Show teams page
     */
    public function index(): View
    {
        return view('teams.index');
    }

    /**
     * AJAX — DataTables server-side
     */
    public function list(Request $request): JsonResponse
    {
        $query = $this->service->getAll();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('status_badge', function($t) {
                $cls   = $t->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500';
                $label = $t->is_active ? 'Active' : 'Inactive';
                return "<button onclick=\"toggleStatus({$t->id})\"
                                class=\"px-3 py-1 rounded-full text-xs font-bold transition-all {$cls}\">
                            {$label}
                        </button>";
            })
            ->addColumn('created_by_name', fn($t) => $t->createdBy?->name ?? '—')
            ->addColumn('created_date', fn($t) => $t->created_at->format('d M Y'))
            ->addColumn('actions', function($t) {
                return '
                    <div class="flex items-center gap-2">
                        <button onclick="openEditModal(' . $t->id . ')" title="Edit"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                                style="background:#e8f2fb"
                                onmouseover="this.style.background=\'#0e6099\'; this.querySelector(\'svg\').style.stroke=\'#ffffff\'"
                                onmouseout="this.style.background=\'#e8f2fb\'; this.querySelector(\'svg\').style.stroke=\'#0e6099\'">
                            <svg class="w-3.5 h-3.5 fill-none stroke-2 transition-all" style="stroke:#0e6099" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <a href="/teams/' . $t->id . '/members" title="View Members"
                           class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                           style="background:#eff6ff"
                           onmouseover="this.style.background=\'#3b82f6\'; this.querySelector(\'svg\').style.stroke=\'#ffffff\'"
                           onmouseout="this.style.background=\'#eff6ff\'; this.querySelector(\'svg\').style.stroke=\'#3b82f6\'">
                            <svg class="w-3.5 h-3.5 fill-none stroke-2" style="stroke:#3b82f6" viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </a>
                        <button onclick="openDeleteModal(' . $t->id . ', \'' . e($t->name) . '\')" title="Delete"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                                style="background:#fef2f2"
                                onmouseover="this.style.background=\'#ef4444\'; this.querySelector(\'svg\').style.stroke=\'#ffffff\'"
                                onmouseout="this.style.background=\'#fef2f2\'; this.querySelector(\'svg\').style.stroke=\'#ef4444\'">
                            <svg class="w-3.5 h-3.5 fill-none stroke-2" style="stroke:#ef4444" viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6"/><path d="M14 11v6"/>
                            </svg>
                        </button>
                    </div>';
            })
            ->filter(function($query) use ($request) {
                if ($request->filter_status !== null && $request->filter_status !== '') {
                    $query->where('is_active', $request->filter_status);
                }
                if ($request->filter_name) {
                    $query->where('name', 'like', "%{$request->filter_name}%");
                }
            }, true)
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    /**
     * AJAX — Get single team for edit
     */
    public function show(Team $team): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $team->id,
                'name'        => $team->name,
                'code'        => $team->code,
                'description' => $team->description,
                'is_active'   => $team->is_active,
            ]
        ]);
    }

    /**
     * AJAX — Create team
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:teams,code'],
        ], [
            'name.required' => 'Team name is required.',
            'code.required' => 'Team code is required.',
            'code.unique'   => 'This team code is already in use.',
        ]);

        try {
            $team = $this->service->create($request->all());
            return response()->json([
                'success' => true,
                'message' => "Team {$team->name} created successfully.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Update team
     */
    public function update(Request $request, Team $team): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', "unique:teams,code,{$team->id}"],
        ], [
            'name.required' => 'Team name is required.',
            'code.required' => 'Team code is required.',
            'code.unique'   => 'This team code is already in use.',
        ]);

        try {
            $team = $this->service->update($team, $request->all());
            return response()->json([
                'success' => true,
                'message' => "Team {$team->name} updated successfully.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Toggle status
     */
    public function toggleStatus(Team $team): JsonResponse
    {
        try {
            $team = $this->service->toggleStatus($team);
            return response()->json([
                'success' => true,
                'message' => "{$team->name} is now " . ($team->is_active ? 'Active' : 'Inactive'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Delete team
     */
    public function destroy(Team $team): JsonResponse
    {
        try {
            $name = $team->name;
            $this->service->delete($team);
            return response()->json([
                'success' => true,
                'message' => "{$name} has been deleted. All members unassigned.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate unique team code
     */
    public function generateCode(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'code'    => $this->service->generateCode(),
        ]);
    }

    /**
     * Show team members page
     */
    public function members(Team $team): View
    {
        $unassignedUsers = $this->service->getUnassignedUsers();
        $activeTeams     = $this->service->getActiveTeams();
        return view('teams.members', compact('team', 'unassignedUsers', 'activeTeams'));
    }

    /**
     * AJAX — Get team members list
     */
    public function membersList(Team $team): JsonResponse
    {
        $query = User::where('team_id', $team->id)
            ->with('roles')
            ->select([
                'id', 'name', 'email',
                'employee_code', 'is_active', 'created_at'
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name_email', function($u) {
                return '
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-primary">' . strtoupper(substr($u->name, 0, 1)) . '</span>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-dark">' . e($u->name) . '</div>
                            <div class="text-xs text-gray-400">' . e($u->email) . '</div>
                        </div>
                    </div>';
            })
            ->addColumn('role_badge', function($u) {
                $role = $u->getRoleNames()->first() ?? '—';
                $config = [
                    'super_admin'        => ['bg-blue-50 text-blue-600',    'Super Admin'],
                    'sub_admin'          => ['bg-indigo-50 text-indigo-600', 'Sub Admin'],
                    'operations_admin'   => ['bg-purple-50 text-purple-600', 'Ops Admin'],
                    'team_member'        => ['bg-green-50 text-green-600',   'Team Member'],
                ];
                [$cls, $label] = $config[$role] ?? ['bg-crm-light text-crm-gray', $role];
                return "<span class=\"px-2 py-1 rounded-full text-xs font-bold {$cls}\">{$label}</span>";
            })
            ->addColumn('status_badge', function($u) {
                $cls   = $u->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500';
                $label = $u->is_active ? 'Active' : 'Inactive';
                return "<span class=\"px-3 py-1 rounded-full text-xs font-bold {$cls}\">{$label}</span>";
            })
            ->addColumn('created_date', fn($u) => $u->created_at->format('d M Y'))
            ->addColumn('actions', function($u) use ($team) {
                return '
                    <button onclick="openRemoveModal(' . $u->id . ', \'' . e($u->name) . '\')"
                            title="Remove from team"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                            style="background:#fff7ed"
                            onmouseover="this.style.background=\'#f97316\'; this.querySelector(\'svg\').style.stroke=\'#ffffff\'"
                            onmouseout="this.style.background=\'#fff7ed\'; this.querySelector(\'svg\').style.stroke=\'#f97316\'">
                        <svg class="w-3.5 h-3.5 fill-none stroke-2" style="stroke:#f97316" viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <line x1="18" y1="8" x2="23" y2="13"/>
                            <line x1="23" y1="8" x2="18" y2="13"/>
                        </svg>
                    </button>';
            })
            ->rawColumns(['name_email', 'role_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    /**
     * AJAX — Assign member to team
     */
    public function assignMember(Request $request, Team $team): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $this->service->assignMember($team, $user);
            return response()->json([
                'success' => true,
                'message' => "{$user->name} assigned to {$team->name}.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Remove member from team
     */
    public function removeMember(Team $team, User $user): JsonResponse
    {
        try {
            $this->service->removeMember($team, $user);
            return response()->json([
                'success' => true,
                'message' => "{$user->name} removed from {$team->name}.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Transfer clients to another team
     */
    public function transferClients(Request $request, Team $team): JsonResponse
    {
        $request->validate([
            'target_team_id' => ['required', 'exists:teams,id', "different:id"],
        ], [
            'target_team_id.required'  => 'Please select a target team.',
            'target_team_id.different' => 'Target team must be different from current team.',
        ]);

        try {
            $targetTeam = Team::findOrFail($request->target_team_id);
            $count      = $this->service->transferClients($team, $targetTeam);
            return response()->json([
                'success' => true,
                'message' => "{$count} clients transferred to {$targetTeam->name}.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}