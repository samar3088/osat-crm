<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamMemberRequest;
use App\Services\TeamMemberService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        try {
            $members = $this->service->getAll($request->search ?? '');

            $data = $members->map(fn($m) => [
                'id'            => $m->id,
                'name'          => $m->name,
                'email'         => $m->email,
                'employee_code' => $m->employee_code ?? '—',
                'work_type'     => $m->work_type,
                'assigned_to'   => $m->assignedTo?->name ?? '—',
                'assigned_to_id'=> $m->assigned_to,
                'clients_count' => $m->clients_count,
                'is_active'     => $m->is_active,
                'created_at'    => $m->created_at->format('d M Y'),
            ]);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
}