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
            $table->renameColumn('employment_type', 'employment_type_id');
            $table->renameColumn('work_arragement', 'work_arragement_id');
            $table->renameColumn('job_application_status', 'job_application_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->renameColumn('employment_type_id', 'employment_type');
            $table->renameColumn('work_arragement_id', 'work_arragement');
            $table->renameColumn('job_application_status_id', 'job_application_status');
        });
    }
};
