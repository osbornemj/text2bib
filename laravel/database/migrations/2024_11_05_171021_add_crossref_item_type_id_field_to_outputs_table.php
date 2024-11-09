<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('outputs', function (Blueprint $table) {
            $table->foreignId('crossref_item_type_id')->after('crossref_item_type')->nullable()->references('id')->on('item_types')->cascadeOnDelete()->cascadeOnUpdate();
        });

        DB::statement('ALTER TABLE outputs MODIFY item_type_id bigint unsigned null');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outputs', function (Blueprint $table) {
            $table->dropColumn(['crossref_item_type_id']);
        });
    }
};
