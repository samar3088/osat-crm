<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Users ─────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('work_type');
            $table->index('assigned_to');
            $table->index('created_at');
        });

        // ── Clients ───────────────────────────────────
        Schema::table('clients', function (Blueprint $table) {
            $table->index('assigned_to');
            $table->index('client_type');
            $table->index('is_active');
            $table->index('created_at');
            $table->index('date_of_birth');
            $table->index(['assigned_to', 'is_active']);
            $table->index(['assigned_to', 'client_type']);
        });

        // ── Conveyances ───────────────────────────────
        Schema::table('conveyances', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('conveyance_type');
            $table->index('conveyance_date');
            $table->index(['user_id', 'status']);
            $table->index(['status', 'conveyance_date']);
        });

        // ── User Targets ──────────────────────────────
        Schema::table('user_targets', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['user_id', 'year', 'month']);
        });

        // ── Activities ────────────────────────────────
        // Columns: id, client_id, client_name, client_pan, client_mobile,
        // client_email, client_type, sales_category, scheme_category,
        // transaction, amount, remarks, full_remarks, activity_date,
        // is_active, created_by, deleted_at, created_at, updated_at
        Schema::table('activities', function (Blueprint $table) {
            $table->index('client_id');
            $table->index('created_by');
            $table->index('activity_date');
            $table->index('created_at');
            $table->index(['created_by', 'activity_date']);
            $table->index(['client_id', 'created_by']);
        });

        // ── Notifications ─────────────────────────────
        // Columns: id, user_id, title, message, type, link,
        // is_read, read_at, deleted_at, created_at, updated_at
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('is_read');
            $table->index('created_at');
            $table->index(['user_id', 'is_read']);
        });

        // ── Audit Logs ────────────────────────────────
        // Columns: id, user_id, user_name, action, module,
        // description, old_values, new_values, ip_address,
        // user_agent, deleted_at, created_at, updated_at
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['work_type']);
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['client_type']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['date_of_birth']);
            $table->dropIndex(['assigned_to', 'is_active']);
            $table->dropIndex(['assigned_to', 'client_type']);
        });

        Schema::table('conveyances', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['conveyance_type']);
            $table->dropIndex(['conveyance_date']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['status', 'conveyance_date']);
        });

        Schema::table('user_targets', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['user_id', 'year', 'month']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['activity_date']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['created_by', 'activity_date']);
            $table->dropIndex(['client_id', 'created_by']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['is_read']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'is_read']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['action']);
            $table->dropIndex(['module']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['module', 'created_at']);
        });
    }
};