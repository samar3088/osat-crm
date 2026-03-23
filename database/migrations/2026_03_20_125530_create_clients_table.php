<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_name', 150);
            $table->string('client_pan', 20)->nullable()->index();
            $table->string('client_mobile', 20)->nullable()->index();
            $table->string('client_email', 150)->nullable();
            $table->string('client_type', 50)->nullable();
            $table->string('sales_category', 50)->nullable();
            $table->string('scheme_category', 50)->nullable();
            $table->string('transaction', 50)->nullable();
            $table->decimal('equity', 15, 2)->default(0);
            $table->decimal('debt', 15, 2)->default(0);
            $table->decimal('hybrid', 15, 2)->default(0);
            $table->decimal('liquid', 15, 2)->default(0);
            $table->decimal('sip_amount', 15, 2)->default(0);
            $table->decimal('lumpsum_amount', 15, 2)->default(0);
            $table->string('source_detail', 255)->nullable();
            $table->text('full_remarks')->nullable();
            $table->text('latest_remarks')->nullable();
            $table->timestamp('latest_remarks_updated_on')->nullable();
            $table->boolean('client_existing')->default(false);
            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('service_team_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->date('date_first_added')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};