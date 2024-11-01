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
        Schema::table('conversions', function (Blueprint $table) {
            $table->boolean('use_crossref')->default(false)->after('include_source');
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->boolean('use_crossref')->default(false)->after('include_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropColumn(['use_crossref']);
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn(['use_crossref']);
        });
    }
};
