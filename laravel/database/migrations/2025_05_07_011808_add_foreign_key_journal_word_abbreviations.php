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
        Schema::table('journal_word_abbreviations', function (Blueprint $table) {
            $table->foreign('output_id')->references('id')->on('outputs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_word_abbreviations', function (Blueprint $table) {
            $table->dropForeign(['output_id']);
        });
    }
};
