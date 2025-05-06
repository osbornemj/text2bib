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
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->integer('training_items_conversion_count')->after('max_checked_conversion_id');
            $table->dateTime('training_items_conversion_started_at')->after('training_items_conversion_count')->nullable();
            $table->dateTime('training_items_conversion_ended_at')->after('training_items_conversion_started_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn(['training_items_conversion_count', 'training_items_conversion_started_at', 'training_items_conversion_ended_at']);
        });
    }
};
