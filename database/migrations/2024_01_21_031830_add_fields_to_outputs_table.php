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
        Schema::table('outputs', function (Blueprint $table) {
            $table->json('warnings')->after('item');
            $table->json('notices')->after('warnings');
            $table->json('details')->after('notices');
            $table->string('scholar_title')->after('details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outputs', function (Blueprint $table) {
            $table->dropColumn('warnings');
            $table->dropColumn('notices');
            $table->dropColumn('details');
            $table->dropColumn('scholar_title');
        });
    }
};
