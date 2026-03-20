<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->nullOnDelete();
            $table->string('client_name', 150)->nullable();
            $table->string('client_pan', 20)->nullable();
            $table->string('client_mobile', 20)->nullable();
            $table->string('client_email', 150)->nullable();
            $table->string('client_type', 50)->nullable();
            $table->string('sales_category', 50)->nullable();
            $table->string('scheme_category', 50)->nullable();
            $table->string('transaction', 50)->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->text('full_remarks')->nullable();
            $table->timestamp('activity_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};