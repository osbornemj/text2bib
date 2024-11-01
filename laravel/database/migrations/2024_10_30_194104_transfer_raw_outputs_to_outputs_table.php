<?php

use App\Models\Output;
use App\Models\RawOutput;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $rawOutputs = RawOutput::all();
        foreach ($rawOutputs as $rawOutput) {
            $output = Output::where('id', $rawOutput->output_id)->first();
            $output->orig_item = $rawOutput->item;
            $output->orig_item_type_id = $rawOutput->item_type_id;
            $output->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
