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
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('task_edit_pending')->default(false);
            $table->json('task_pending_data')->nullable(); // use text() instead of json() if your DB doesn't support json
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // This will remove the columns if you ever rollback the migration
            $table->dropColumn(['task_edit_pending', 'task_pending_data']);
        });
    }
};