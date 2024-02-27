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
            $table->string('label');
            $table->json('item');
            $table->integer('seq');
            $table->tinyInteger('correctness')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outputs');
    }
};
