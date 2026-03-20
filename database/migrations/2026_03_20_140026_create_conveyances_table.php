<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conveyances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('conveyance_type', 100);
            $table->decimal('amount', 10, 2);
            $table->text('remarks')->nullable();
            $table->string('bill_path', 500)->nullable();
            $table->date('conveyance_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');
            $table->foreignId('actioned_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('action_remarks')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conveyances');
    }
};