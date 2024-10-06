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
            $table->string('type')->nullable()->after('name');
            $table->string('style_required')->nullable()->after('type');
            $table->boolean('ctan')->default(false)->after('style_required');
            $table->dropColumn('natbib');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bsts', function (Blueprint $table) {
            $table->dropColumn(['type', 'style_required', 'ctan']);            
            $table->boolean('natbib')->default(false)->after('name');
        });
    }
};
