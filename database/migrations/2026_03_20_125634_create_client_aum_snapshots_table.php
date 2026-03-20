<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_aum_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->nullOnDelete();
            $table->string('client_name', 150)->nullable();
            $table->string('client_pan', 20)->nullable();
            $table->string('client_email', 150)->nullable();
            $table->string('client_mobile', 20)->nullable();
            $table->decimal('aum', 15, 2)->default(0);
            $table->foreignId('employee_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('employee_code', 50)->nullable();
            $table->date('registered_on')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_aum_snapshots');
    }
};