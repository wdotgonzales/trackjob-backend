<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->renameColumn('employment_type_id', 'employment_types_id');
            $table->renameColumn('work_arragement_id', 'work_arragements_id');
            $table->renameColumn('job_application_status_id', 'job_application_statuses_id');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->renameColumn('employment_types_id', 'employment_type_id');
            $table->renameColumn('work_arragements_id', 'work_arragement_id');
            $table->renameColumn('job_application_statuses_id', 'job_application_status_id');
        });
    }
};
