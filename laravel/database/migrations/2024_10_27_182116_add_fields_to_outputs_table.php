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
            $table->foreignId('orig_item_type_id')->after('item')->nullable()->references('id')->on('item_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->json('orig_item')->nullable()->after('orig_item_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outputs', function (Blueprint $table) {
            $table->dropForeign(['orig_item_type']);
            $table->dropColumn(['orig_item_type_id', 'orig_item']);
        });
    }
};
