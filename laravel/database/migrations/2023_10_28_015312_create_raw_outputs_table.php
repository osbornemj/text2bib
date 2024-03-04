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
            $table->json('item');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_outputs');
    }
};
