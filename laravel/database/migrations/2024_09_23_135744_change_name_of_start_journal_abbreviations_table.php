<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('start_journal_abbreviations', 'journal_word_abbreviations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('journal_word_abbreviations', 'start_journal_abbreviations');
    }
};
