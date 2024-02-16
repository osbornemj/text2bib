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
        Schema::create('raw_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('output_id')->references('id')->on('outputs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('item_type_id')->references('id')->on('item_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('raw_output_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_output_id')->references('id')->on('raw_outputs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('item_field_id')->references('id')->on('item_fields')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('content')->nullable();
            $table->integer('seq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_output_fields', function (Blueprint $table) {
            $table->dropForeign(['raw_output_id', 'item_field_id']);
        });

        Schema::dropIfExists('raw_output_fields');

        Schema::table('raw_outputs', function (Blueprint $table) {
            $table->dropForeign(['output_id']);
        });

        Schema::dropIfExists('raw_outputs');
    }
};
