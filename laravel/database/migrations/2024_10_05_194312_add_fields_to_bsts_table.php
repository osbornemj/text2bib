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
            $table->boolean('available')->default(false)->after('note');
            $table->boolean('checked')->default(false)->after('available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bsts', function (Blueprint $table) {
            $table->dropColumn(['available', 'checked']);
        });
    }
};
