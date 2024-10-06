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
        Schema::create('bsts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('natbib')->default(false);
            $table->boolean('doi')->default(false);
            $table->boolean('url')->default(false);
            $table->boolean('urldate')->default(false);
            $table->boolean('doi_escape_underscore')->default(false);
            $table->boolean('proc_address_conf_location')->default(false);
            $table->boolean('translator')->default(false);
            $table->boolean('online')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('conversions', function (Blueprint $table) {
            $table->foreignId('bst_id')->nullable()->after('bst')->references('id')->on('bsts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dropColumn('bst');
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->foreignId('bst_id')->nullable()->after('bst')->references('id')->on('bsts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dropColumn('bst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bsts');
    }
};
