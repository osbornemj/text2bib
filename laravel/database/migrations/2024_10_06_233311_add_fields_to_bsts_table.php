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
        Schema::table('bsts', function (Blueprint $table) {
            $table->boolean('eid')->default(false)->after('urldate');
            $table->boolean('isbn')->default(false)->after('eid');
            $table->boolean('issn')->default(false)->after('isbn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bsts', function (Blueprint $table) {
            $table->dropColumn(['eid', 'isbn', 'issn']);
        });
    }
};
