<?php

use App\Models\Comment;
use App\Models\ErrorReportComment;
use App\Models\User;
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
        Schema::create('required_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->cascadeOnDelete()->cascadeOnUpdate()->constrained();
            $table->foreignIdFor(ErrorReportComment::class)->nullable()->cascadeOnDelete()->cascadeOnUpdate()->constrained();
            $table->foreignIdFor(Comment::class)->nullable()->cascadeOnDelete()->cascadeOnUpdate()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('required_responses');
    }
};
