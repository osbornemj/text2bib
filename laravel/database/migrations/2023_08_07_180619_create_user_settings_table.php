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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('item_separator')->default('line');
            $table->string('label_style')->default('short');
            $table->boolean('override_labels')->default(false);
            $table->char('line_endings', 1)->default('w');
            $table->string('char_encoding')->default('utf8');
            $table->boolean('percent_comment')->default(true);
            $table->boolean('include_source')->default(true);
            $table->string('report_type')->default('standard');
            $table->tinyInteger('save_settings')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
