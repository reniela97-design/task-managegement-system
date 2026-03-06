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
        $table->date('due_date')->nullable()->after('description');
        $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('due_date');
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn(['due_date', 'priority']);
    });
}
};
