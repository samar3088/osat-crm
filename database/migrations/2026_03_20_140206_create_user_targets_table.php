<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->year('year');
            $table->tinyInteger('month');
            $table->string('plan', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('category', 50)->nullable();
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->decimal('target_amount_achieved', 15, 2)->default(0);
            $table->integer('target_investors')->default(0);
            $table->integer('target_investors_achieved')->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['user_id','year','month','type'], 'unique_target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_targets');
    }
};