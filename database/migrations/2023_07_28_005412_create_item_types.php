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
        Schema::create('item_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('item_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('item_field_item_type', function (Blueprint $table) {
            $table->foreignId('item_field_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('item_type_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_types');
        Schema::dropIfExists('item_fields');

        Schema::table('item_field_item_type', function (Blueprint $table) {
            $table->dropForeign(['item_field_id']);
            $table->dropForeign(['item_type_id']);
        });

        Schema::dropIfExists('item_field_item_type');
    }
};
