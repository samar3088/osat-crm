<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientAumSnapshot;
use App\Models\ClientSip;
use App\Models\Activity;
use App\Models\Conveyance;
use App\Models\UserTarget;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get all stat tiles data
     * Scoped by role — Super Admin sees all, RM sees own only
     */
    public function getStats(): array
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $totalClients  = Client::count();
            $totalAum      = ClientAumSnapshot::sum('aum');
            $sipClients    = ClientSip::where('is_active', true)->count();
            $totalSip      = ClientSip::where('is_active', true)->sum('amount');
        } else {
            // Team Member — scoped to assigned clients only
            $totalClients  = Client::where('assigned_to', $user->id)->count();
            $totalAum      = ClientAumSnapshot::where('employee_id', $user->id)->sum('aum');
            $sipClients    = ClientSip::where('team_member_id', $user->id)->where('is_active', true)->count();
            $totalSip      = ClientSip::where('team_member_id', $user->id)->where('is_active', true)->sum('amount');
        }

        return [
            'total_clients' => number_format($totalClients),
            'total_aum'     => $this->formatCrore($totalAum),
            'sip_clients'   => number_format($sipClients),
            'total_sip'     => $this->formatCrore($totalSip),
        ];
    }

    /**
     * Get target progress for current month
     */
    public function getTargets(): array
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            // Super Admin sees combined team targets
            $sipTarget = UserTarget::where('year', now()->year)
                ->where('month', now()->month)
                ->where('type', 'SIP')
                ->sum('target_amount');

            $sipAchieved = UserTarget::where('year', now()->year)
                ->where('month', now()->month)
                ->where('type', 'SIP')
                ->sum('target_amount_achieved');

            $lumpsumTarget = UserTarget::where('year', now()->year)
                ->where('month', now()->month)
                ->where('type', 'Lumpsum')
                ->sum('target_amount');

            $lumpsumAchieved = UserTarget::where('year', now()->year)
                ->where('month', now()->month)
                ->where('type', 'Lumpsum')
                ->sum('target_amount_achieved');
        } else {
            // Team Member — own targets only
            $sipTarget = UserTarget::where('user_id', $user->id)
                ->where('year', now()->year)
                ->where('month', now()->month)
                ->where('type', 'SIP')
                ->value('target_amount') ?? 0;

            $sipAchieved = UserTarget::where('user_id', $user->id)
                ->where('year', now()->year)
                ->where('month', now()->month)
                ->where('type', 'SIP')
                ->value('target_amount_achieved') ?? 0;

            $lumpsumTarget = UserTarget::where('user_id', $user->id)
                ->where('year', now()->year)
                ->where('month', now()->month)
                ->where('type', 'Lumpsum')
                ->value('target_amount') ?? 0;

            $lumpsumAchieved = UserTarget::where('user_id', $user->id)
                ->where('year', now()->year)
                ->where('month', now()->month)
                ->where('type', 'Lumpsum')
                ->value('target_amount_achieved') ?? 0;
        }

        return [
            'sip' => [
                'target'     => $this->formatCrore($sipTarget),
                'achieved'   => $this->formatCrore($sipAchieved),
                'percentage' => $sipTarget > 0 ? min(round(($sipAchieved / $sipTarget) * 100) , 100) : 0,
            ],
            'lumpsum' => [
                'target'     => $this->formatCrore($lumpsumTarget),
                'achieved'   => $this->formatCrore($lumpsumAchieved),
                'percentage' => $lumpsumTarget > 0 ? min(round(($lumpsumAchieved / $lumpsumTarget) * 100), 100) : 0,
            ],
        ];
    }

    /**
     * Get AUM growth trend — last 6 months
     */
    public function getAumTrend(): array
    {
        $user   = auth()->user();
        $months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $query = ClientAumSnapshot::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            if (!$user->isSuperAdmin()) {
                $query->where('employee_id', $user->id);
            }

            $months->push([
                'month' => $date->format('M Y'),
                'aum'   => round($query->sum('aum') / 10000000, 2), // Convert to Crore
            ]);
        }

        return [
            'labels' => $months->pluck('month')->toArray(),
            'data'   => $months->pluck('aum')->toArray(),
        ];
    }

    /**
     * Get recent activities — last 5
     */
    public function getRecentActivities(): array
    {
        $user  = auth()->user();
        $query = Activity::with('createdBy')
            ->where('is_active', true)
            ->latest()
            ->limit(5);

        if (!$user->isSuperAdmin()) {
            $query->where('created_by', $user->id);
        }

        return $query->get()->map(function ($activity) {
            return [
                'id'           => $activity->id,
                'client_name'  => $activity->client_name,
                'transaction'  => $activity->transaction,
                'amount'       => '₹' . number_format($activity->amount, 2),
                'remarks'      => $activity->remarks,
                'created_by'   => $activity->createdBy?->name,
                'date'         => $activity->activity_date?->format('d M Y') ?? $activity->created_at->format('d M Y'),
            ];
        })->toArray();
    }

    /**
     * Get pending conveyances — Super Admin only
     */
    public function getPendingConveyances(): array
    {
        if (!auth()->user()->isSuperAdmin()) return [];

        return Conveyance::with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($c) {
                return [
                    'id'               => $c->id,
                    'user_name'        => $c->user->name,
                    'conveyance_type'  => $c->conveyance_type,
                    'amount'           => '₹' . number_format($c->amount, 2),
                    'date'             => $c->conveyance_date->format('d M Y'),
                ];
            })->toArray();
    }

    /**
     * Format number to Indian Crore format
     */
    private function formatCrore(float $amount): string
    {
        if ($amount >= 10000000) {
            return '₹' . number_format($amount / 10000000, 2) . ' Cr';
        } elseif ($amount >= 100000) {
            return '₹' . number_format($amount / 100000, 2) . ' L';
        }
        return '₹' . number_format($amount, 2);
    }
}