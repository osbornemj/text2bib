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
        Schema::table('bsts', function (Blueprint $table) {
            $table->boolean('eprinttype')->default(false)->after('eid');
            $table->boolean('archiveprefix')->default(false)->after('eprinttype');
            $table->boolean('eprint')->default(false)->after('archiveprefix');
            $table->boolean('eprintclass')->default(false)->after('eprint');
            $table->boolean('primaryclass')->default(false)->after('eprintclass');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bsts', function (Blueprint $table) {
            $table->dropColumn(['eprinttype', 'archiveprefix', 'eprint', 'eprintclass', 'primaryclass']);
        });
    }
};
