<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\UserFile;

use App\Traits\AddLabels;

use App\Services\Converter;
use Illuminate\Support\Facades\Request;

class ConversionAdminController extends Controller
{
    use AddLabels;

    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    public function index(): View
    {
        $conversions = Conversion::orderByDesc('created_at')
            ->with('user')
            ->withCount('outputs')
            ->paginate(50);

        return view('admin.conversions.index', compact('conversions'));
    }

    public function showConversion(int $conversionId, int $page): View
    {
        $outputs = Output::where('conversion_id', $conversionId)
                    ->with('itemType')
                    ->orderBy('seq')
                    ->get();

        $conversion = Conversion::find($conversionId);

        return view('admin.conversions.show', compact('outputs', 'conversion', 'page'));
    }

    public function destroy(int $conversionId)
    {
        $conversion = Conversion::find($conversionId);
        $conversion->delete();

        return back();
    }

    public function examined(int $conversionId, int $page)
    {
        $conversion = Conversion::find($conversionId);
        $conversion->update(['examined_at' => now()]);

        return redirect('admin/conversions?page=' . $page . '#' . $conversionId);
    }

    public function unexamined(int $conversionId)
    {
        $conversion = Conversion::find($conversionId);
        $conversion->update(['examined_at' => null]);

        return back();
    }

    public function downloadSource(int $userFileId)
    {
        $userFile = UserFile::find($userFileId);
        return Storage::download('public/files/' . $userFile->user_id . '-' . $userFileId . '-source.txt');
    }

    // Converts entries in exising file according to parameters of Conversion with id = $conversionId.
    // Duplicate of code in Livewire component ConvertFile with a few changes: e.g. return ... -> redirect ....
    // Can duplication easily be avoided?
    public function convert(int $fileId, string|null $itemSeparator = null): View
    {
        if (! $itemSeparator) {
            $itemSeparator = 'line';
        }

        $conversion = new Conversion;
        if ($itemSeparator) {
            $conversion->item_separator = $itemSeparator;
        }
        $conversion->report_type == 'detailed';
        $conversion->user_file_id = $fileId;
        $conversion->user_id = Auth::id();
        $conversion->save();

        // Get file that user uploaded
        // If file no longer exists, could resort to getting source of each item from outputs table
        $filestring = Storage::disk('public')->get('files/' . $conversion->user_id . '-' . $conversion->user_file_id . '-source.txt');

        // Regularlize line-endings
        $filestring = str_replace(["\r\n", "\r"], "\n", $filestring);

        $entries = explode($conversion->item_separator == 'line' ? "\n\n" : "\n", $filestring);

        if (count($entries) == 1 && strlen($entries[0]) > 500) {
            $entry = $entries[0];
            return view('index.itemSeparatorError', compact('entry', 'conversionId'));
        }

        // Check for utf-8
        $nonUtf8Entries = [];
        foreach ($entries as $entry) {
            if (!mb_check_encoding($entry)) {
                $nonUtf8Entries[] = $entry;
            }
        }

        if (count($nonUtf8Entries)) {
            return view('index.encodingError', compact('nonUtf8Entries'));
        }

        $convertedEntries = [];
        foreach ($entries as $entry) {
            // $convertedEntries is array with components 'source', 'item', 'itemType', 'label', 'warnings',
            // 'notices', 'details'.
            // 'label' (which depends on whole set of converted items) is updated later
            $convertedEntry = $this->converter->convertEntry($entry, $conversion);
            if ($convertedEntry) {
                $convertedEntries[] = $convertedEntry;
            }
        }

        // $this->addLabels from AddLabbels trait
        $convertedEntries = $this->addLabels($convertedEntries, $conversion);

        $itemTypes = ItemType::all();

        // Write converted items to database and key array to output ids
        $convertedItems = [];
        foreach ($convertedEntries as $i => $convItem) {
            $output = Output::create([
                'source' => $convItem['source'],
                'conversion_id' => $conversion->id,
                'item_type_id' => $itemTypes->where('name', $convItem['itemType'])->first()->id,
                'label' => $convItem['label'],
                'item' => $convItem['item'],
                'seq' => $i,
            ]);
            $convertedItems[$output->id] = $convItem;
        }

        $itemTypeOptions = $itemTypes->pluck('name', 'id')->all();
        $conversionId = $conversion->id;
        $includeSource = $conversion->include_source;
        $reportType = $conversion->report_type;

        return view('index.bibtex',
            compact(
                'convertedItems',
                'itemTypes',
                'itemTypeOptions',
                'conversionId',
                'includeSource',
                'reportType'
            )
        );
    }

    public function formatExample(int $outputId): View
    {
        $output = Output::find($outputId);
        $itemType = ItemType::find($output->item_type_id);

        return view('admin.formatExample', compact('output', 'itemType'));
    }
}
