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
        Schema::table('publishers', function (Blueprint $table) {
            $table->tinyInteger('distinctive')->default(1)->after('name');
            $table->tinyInteger('checked')->default(0)->after('distinctive');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->tinyInteger('distinctive')->default(1)->after('name');
            $table->tinyInteger('checked')->default(0)->after('distinctive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publishers', function (Blueprint $table) {
            $table->dropColumn('distinctive');
            $table->dropColumn('checked');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('distinctive');
            $table->dropColumn('checked');
        });
    }
};
