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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->foreign('employment_type_id')->references('id')->on('employment_types')->onDelete('cascade');
            $table->foreign('work_arragement_id')->references('id')->on('work_arrangements')->onDelete('cascade');
            $table->foreign('job_application_status_id')->references('id')->on('job_application_statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['employment_type_id']);
            $table->dropForeign(['work_arragement_id']);
            $table->dropForeign(['job_application_status_id']);
        });
    }
};
