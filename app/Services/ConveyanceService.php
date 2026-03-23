<?php

namespace App\Services;

use App\Models\Conveyance;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Mail\ConveyanceStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ConveyanceService
{
    /**
     * Get conveyances — scoped by role
     */
    public function getAll(): \Illuminate\Support\Collection
    {
        $user  = auth()->user();
        $query = Conveyance::with(['user', 'actionedBy'])->latest();

        // Team Member sees only their own
        if (!$user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query->get()->map(fn($c) => [
            'id'              => $c->id,
            'user_id'         => $c->user_id,
            'team_member'     => $c->user->name ?? '—',
            'conveyance_type' => $c->conveyance_type,
            'conveyance_date' => $c->conveyance_date->format('d M Y'),
            'amount'          => number_format($c->amount, 2),
            'remarks'         => $c->remarks ?? '—',
            'bill_path'       => $c->bill_path ? asset('storage/' . $c->bill_path) : null,
            'status'          => $c->status,
            'action_remarks'  => $c->action_remarks ?? '—',
            'actioned_by'     => $c->actionedBy?->name ?? '—',
            'actioned_at'     => $c->actioned_at?->format('d M Y') ?? '—',
            'created_at'      => $c->created_at->format('d M Y'),
        ]);
    }

    /**
     * Create conveyance claim
     */
    public function create(array $data, $file = null): Conveyance
    {
        $billPath = null;

        if ($file) {
            $billPath = $file->store('conveyances', 'public');
        }

        $conveyance = Conveyance::create([
            'user_id'          => auth()->id(),
            'conveyance_type'  => $data['conveyance_type'],
            'conveyance_date'  => $data['conveyance_date'],
            'amount'           => $data['amount'],
            'remarks'          => $data['remarks'] ?? null,
            'bill_path'        => $billPath,
            'status'           => 'pending',
        ]);

        AuditLog::record(
            'created_conveyance',
            'conveyance',
            "Submitted conveyance claim of ₹{$data['amount']}"
        );

        return $conveyance;
    }

    /**
     * Approve conveyance
     */
    public function approve(Conveyance $conveyance, string $remarks = ''): Conveyance
    {
        $conveyance->update([
            'status'         => 'approved',
            'actioned_by'    => auth()->id(),
            'action_remarks' => $remarks,
            'actioned_at'    => now(),
        ]);

        // Notify team member
        Notification::send(
            userId:  $conveyance->user_id,
            title:   'Conveyance Approved ✅',
            message: "Your conveyance claim of ₹{$conveyance->amount} has been approved.",
            type:    'conveyance',
            link:    '/conveyance'
        );

        // Send email
        $this->sendStatusEmail($conveyance);

        AuditLog::record(
            'approved_conveyance',
            'conveyance',
            "Approved conveyance #{$conveyance->id}"
        );

        return $conveyance->fresh();
    }

    /**
     * Reject conveyance
     */
    public function reject(Conveyance $conveyance, string $remarks = ''): Conveyance
    {
        $conveyance->update([
            'status'         => 'rejected',
            'actioned_by'    => auth()->id(),
            'action_remarks' => $remarks,
            'actioned_at'    => now(),
        ]);

        // Notify team member
        Notification::send(
            userId:  $conveyance->user_id,
            title:   'Conveyance Rejected ❌',
            message: "Your conveyance claim of ₹{$conveyance->amount} has been rejected. Reason: {$remarks}",
            type:    'conveyance',
            link:    '/conveyance'
        );

        // Send email
        $this->sendStatusEmail($conveyance);

        AuditLog::record(
            'rejected_conveyance',
            'conveyance',
            "Rejected conveyance #{$conveyance->id}. Reason: {$remarks}"
        );

        return $conveyance->fresh();
    }

    /**
     * Soft delete
     */
    public function delete(Conveyance $conveyance): void
    {
        // Delete bill file if exists
        if ($conveyance->bill_path) {
            Storage::disk('public')->delete($conveyance->bill_path);
        }

        AuditLog::record(
            'deleted_conveyance',
            'conveyance',
            "Deleted conveyance #{$conveyance->id}"
        );

        $conveyance->delete();
    }

    /**
     * Send status email to team member
     */
    private function sendStatusEmail(Conveyance $conveyance): void
    {
        try {
            Mail::to($conveyance->user->email)
                ->send(new ConveyanceStatusMail($conveyance));
        } catch (\Exception $e) {
            \App\Models\SystemLog::error(
                "Conveyance status email failed: " . $e->getMessage(),
                'conveyance'
            );
        }
    }
}