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
        Schema::create('outputs', function (Blueprint $table) {
            $table->id();
            $table->text('source');
            $table->foreignId('conversion_id')->references('id')->on('conversions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('item_type_id')->references('id')->on('item_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('seq');
            $table->timestamps();
        });

        Schema::create('output_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('output_id')->references('id')->on('outputs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('item_field_id')->references('id')->on('item_fields')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('content')->nullable();
            $table->integer('seq');
        });

        Schema::table('item_types', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });

        Schema::table('item_fields', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('output_fields', function (Blueprint $table) {
            $table->dropForeign(['output_id', 'item_field_id']);
        });
        Schema::table('outputs', function (Blueprint $table) {
            $table->dropForeign(['conversion_id', 'item_type_id']);
        });
        
        Schema::dropIfExists('output_fields');
        Schema::dropIfExists('outputs');
    }
};
