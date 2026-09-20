<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('automation_status')
                ->default('pending')
                ->after('status');

            $table->unsignedTinyInteger('automation_attempts')
                ->default(0)
                ->after('automation_status');

            $table->timestamp('automation_last_attempted_at')
                ->nullable()
                ->after('automation_attempts');

            $table->timestamp('automation_processed_at')
                ->nullable()
                ->after('automation_last_attempted_at');

            $table->index('automation_status');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['automation_status']);

            $table->dropColumn([
                'automation_status',
                'automation_attempts',
                'automation_last_attempted_at',
                'automation_processed_at',
            ]);
        });
    }
};
