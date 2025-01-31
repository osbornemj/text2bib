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
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->references('id')->on('threads')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['thread_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('threads');

        Schema::dropIfExists('comments');
    }
};
