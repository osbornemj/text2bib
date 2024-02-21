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
        Schema::create('examples', function (Blueprint $table) {
            $table->id();
            $table->text('source');
            $table->string('type');
            $table->timestamps();
        });
        
        Schema::create('example_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('example_id')->references('id')->on('examples')->cascadeOnDelete()->cascadeOnUpdate();            
            $table->string('name');
            $table->text('content');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examples');

        Schema::table('example_fields', function (Blueprint $table) {
            $table->dropForeign(['example_id']);
        });

        Schema::dropIfExists('example_fields');
    }
};
