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
            $table->string('use')->nullable()->after('user_file_id');
            $table->string('other_use')->nullable()->after('use');
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->string('use')->nullable()->after('user_id');
            $table->string('other_use')->nullable()->after('use');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropColumn(['use', 'other_use']);
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn(['use', 'other_use']);
        });
    }
};
