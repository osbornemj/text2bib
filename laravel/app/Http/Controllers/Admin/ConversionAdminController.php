<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\User;
use App\Models\UserFile;
use App\Models\Version;
use App\Traits\AddLabels;

use App\Services\Converter;
use Illuminate\Foundation\Http\FormRequest;

class ConversionAdminController extends Controller
{
    use AddLabels;

    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    public function index(int $userId = 0): View
    {
        $conversions = Conversion::orderByDesc('created_at');

        $user = null;

        if ($userId) {
            $conversions = $conversions->where('user_id', $userId);
            $user = User::find($userId);
        }

        $conversions = $conversions
            ->with('user')
            ->withCount('outputs')
            ->paginate(50);

        return view('admin.conversions.index', compact('conversions', 'user'));
    }

    public function showConversion(int $conversionId, int $page): View
    {
        $outputs = Output::where('conversion_id', $conversionId)
                    ->with('itemType')
                    ->orderBy('seq')
                    ->get();

        $authorPatternCount = $outputs->whereNotNull('author_pattern')->count();
        $outputCount = $outputs->count();
        $authorPatternPercent = $outputCount ? 100 * $authorPatternCount / $outputCount : 0;

        $conversion = Conversion::find($conversionId);

        return view('admin.conversions.show',
            compact(
                'outputs', 
                'conversion', 
                'page', 
                'authorPatternCount', 
                'authorPatternPercent', 
                'outputCount'
            )
        );
    }

    public function destroy(int $conversionId)
    {
        $conversion = Conversion::find($conversionId);
        $conversion->delete();

        return back();
    }

    public function examined(FormRequest $request)
    {
        $conversionId = $request->get('conversionId');
        $page = $request->get('page');
        $adminComment = $request->get('admin_comment');

        $conversion = Conversion::find($conversionId);
        $conversion->update(['examined_at' => now(), 'admin_comment' => $adminComment]);

        return redirect('admin/conversions?page=' . $page . '#' . $conversionId);
    }

    public function unexamined(int $conversionId)
    {
        $conversion = Conversion::find($conversionId);
        $conversion->update(['examined_at' => null, 'admin_comment' => null]);

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

        $version = Version::latest()->first()->created_at;

        $userConversion = Conversion::where('user_file_id', $fileId)->first();

        $conversion = new Conversion;
        if ($itemSeparator) {
            $conversion->item_separator = $itemSeparator;
        }
        $conversion->language = $userConversion->language;
        $conversion->report_type == 'detailed';
        $conversion->user_file_id = $fileId;
        $conversion->user_id = Auth::id();
        $conversion->version = $version;
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

    public function search(): View
    {
        $searchString = request()->search_string;
        $cutoffDate = request()->cutoff_date;
        $searchTerms = explode(' ', $searchString);

        $outputs = Output::with('itemType')
            ->with('conversion');

        if ($cutoffDate) {
            $outputs = $outputs->where('created_at', '>', $cutoffDate);
        }

        foreach ($searchTerms as $searchTerm) {
            $outputs = $outputs->where('source', 'like', '%' . $searchTerm .'%');
        }
        $outputs = $outputs->get();

        return view('admin.conversions.showOutputs', compact('outputs', 'searchString', 'cutoffDate'));
    }

}
