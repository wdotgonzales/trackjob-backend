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
        Schema::table('employment_types', function (Blueprint $table) {
            $table->renameColumn('name', 'title');
            $table->string('description', 255)->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employment_types', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
            $table->dropColumn('description');
        });
    }
};
