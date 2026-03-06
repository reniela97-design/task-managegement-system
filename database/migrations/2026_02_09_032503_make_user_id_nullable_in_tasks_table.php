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
        // Allow user_id to be NULL (Unassigned)
        $table->foreignId('user_id')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        // Revert change (be careful, this fails if you have nulls)
        $table->foreignId('user_id')->nullable(false)->change();
    });
}
};
