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
        Schema::create('lead_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('intent');
            $table->string('urgency');
            $table->unsignedTinyInteger('lead_score');
            $table->text('summary');
            $table->text('recommended_action');
            $table->decimal('confidence', 4, 3)->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->unique('lead_id');
            $table->index('intent');
            $table->index('urgency');
            $table->index('lead_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_analyses');
    }
};
