<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();

            // Log classification
            $table->enum('type', ['error', 'warning', 'info', 'debug'])
                  ->default('info')
                  ->index();

            $table->string('page', 150)->nullable();
            $table->text('detail')->nullable();
            $table->text('stack_trace')->nullable(); // 👈 full error stack

            // Who triggered it
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('username', 150)->nullable();
            $table->string('ip_address', 45)->nullable();

            // Date-wise indexing 👈
            $table->date('log_date')->index();         // for date filtering
            $table->timestamp('logged_at')->index();   // exact time

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};