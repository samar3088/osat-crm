<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * Show dashboard page — empty shell
     * Data loads via AJAX
     */
    public function index(): View
    {
        return view('dashboard.index');
    }

    /**
     * AJAX — Get stat tiles
     */
    public function stats(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getStats();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Get target progress
     */
    public function targets(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getTargets();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Get AUM trend chart data
     */
    public function aumTrend(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getAumTrend();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Get recent activities
     */
    public function recentActivities(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getRecentActivities();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Get pending conveyances (Super Admin only)
     */
    public function pendingConveyances(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getPendingConveyances();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Get today's birthdays
     */
    public function todayBirthdays(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getTodayBirthdays();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Get today's meetings
     */
    public function todayMeetings(): JsonResponse
    {
        try {
            $data = $this->dashboardService->getTodayMeetings();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}