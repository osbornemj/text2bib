<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\UserFile;

use Illuminate\Http\RedirectResponse;

class ConversionAdminController extends Controller
{
    public function index()
    {
        $conversions = Conversion::orderByDesc('created_at')
            ->with('userFile.user')
            ->paginate(config('constants.items_page'));

        return view('admin.conversions.index', compact('conversions'));
    }

    public function showConversion(int $conversionId): View
    {
        $outputs = Output::where('conversion_id', $conversionId)
                    ->with('itemType')
                    ->orderBy('seq')
                    ->get();

        return view('admin.conversions.show', compact('outputs'));
    }

    public function convert(int $fileId, string|null $itemSeparator = null): RedirectResponse
    {
        if (!$itemSeparator) {
            $itemSeparator = 'line';
        }

        $conversion = new Conversion;
        if ($itemSeparator) {
            $conversion->item_separator = $itemSeparator;
        }
        $conversion->report_type == 'detailed';
        $conversion->user_file_id = $fileId;
        $conversion->save();

        return redirect()->route('file.convert', ['conversionId' => $conversion->id]);        
    }

    public function formatExample(int $outputId): View
    {
        $output = Output::find($outputId);
        $itemType = ItemType::find($output->item_type_id);

        return view('admin.formatExample', compact('output', 'itemType'));
    }
}
