<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            // Ensure job_application_id column exists and is unsigned
            $table->unsignedBigInteger('job_application_id')->change();

            // Add foreign key constraint
            $table->foreign('job_application_id')
                ->references('id')
                ->on('job_applications')
                ->onDelete('cascade'); // Adjust as needed (e.g., 'set null')
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropForeign(['job_application_id']);

        });
    }
};
