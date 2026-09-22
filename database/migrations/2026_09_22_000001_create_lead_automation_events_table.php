<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lead_automation_events', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key')->unique();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->string('event')->default('lead.created');
            $table->string('status')->default('pending');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_automation_events');
    }
};
