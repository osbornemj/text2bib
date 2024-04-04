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
            $table->boolean('is_bibtex')->default(false)->after('non_utf8_detected');
            $table->string('orig_item_separator')->nullable()->after('is_bibtex');
        });

        Schema::table('outputs', function (Blueprint $table) {
            $table->string('detected_encoding')->default('UTF-8')->after('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropColumn(['is_bibtex', 'orig_item_separator']);
        });

        Schema::table('outputs', function (Blueprint $table) {
            $table->dropColumn(['detected_encoding']);
        });

    }
};
