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
        Schema::table('examples', function (Blueprint $table) {
            $table->string('char_encoding')->default('utf8')->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examples', function (Blueprint $table) {
            $table->dropColumn(['char_encoding']);
        });
    }
};
