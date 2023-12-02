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
        Schema::table('output_fields', function (Blueprint $table) {
            $table->dropForeign(['output_id']);
            $table->dropForeign(['item_field_id']);
        });

        Schema::table('raw_output_fields', function (Blueprint $table) {
            $table->dropForeign(['raw_output_id']);
            $table->dropForeign(['item_field_id']);
        });
        
        Schema::dropIfExists('output_fields');
        Schema::dropIfExists('raw_output_fields');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
