<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->string('file_error')->nullable()->after('non_utf8_detected');
        });

        DB::statement('UPDATE conversions SET file_error = \'bibtex\' WHERE is_bibtex = 1');

        Schema::table('conversions', function (Blueprint $table) {
            $table->dropColumn(['is_bibtex']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropColumn(['file_error']);
            $table->boolean('is_bibtex')->nullable()->after('non_utf8_detected');
        });
    }
};
