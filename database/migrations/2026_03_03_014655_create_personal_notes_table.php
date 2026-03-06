<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('personal_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Links note to the user
            $table->date('note_date'); // The date clicked on the calendar
            $table->text('note_text')->nullable();
            $table->timestamps();

            // Ensure a user can only have one note per specific date
            $table->unique(['user_id', 'note_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('personal_notes');
    }
};