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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on(table: 'users')->onDelete('cascade');
            $table->text('position_title');
            $table->text('company_name');
            $table->unsignedBigInteger('employment_type');
            // -- add foreign key for employment_type ---
            $table->unsignedBigInteger('work_arragement');
            // -- add foreign key for work_arragement ---
            $table->unsignedBigInteger('job_application_status');
            // -- add foreign key for job_application_status ---
            $table->text('job_posting_link');
            $table->date('date_applied');
            $table->text('company_logo_url')->nullable();
            $table->text('job_location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
