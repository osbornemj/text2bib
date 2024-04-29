<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\View\View;

use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\UserFile;

use App\Traits\MakeScholarTitle;

class IndexController extends Controller
{
    use MakeScholarTitle;

    public function index()
    {
        $user = Auth::user();

        return view('welcome', compact('user'));
    }

    public function convertFile(): View
    {
        return view('convert');
    }

    public function about(): View
    {
        return view('about');
    }

    public function requiredResponses(): View
    {
        $user = Auth::user();
        $requiredResponses = $user->requiredResponses;

        return view('requiredResponses', compact('requiredResponses'));
    }

    public function conversions(): View
    {
        $user = Auth::user();
        $conversions = $user->conversions->loadCount('outputs')->sortByDesc('created_at')->paginate(50);

        return view('conversions', compact('conversions'));
    }

    public function showConversion(int $conversionId, int $redirected = 0): View
    {
        $conversion = Conversion::find($conversionId);

        if (!$conversion || $conversion->user_id != Auth::id()) {
            abort(403);
        }

        $outputs = Output::where('conversion_id', $conversionId)
                    ->with('itemType')
                    ->orderBy('seq')
                    ->get();

        $convertedItems = [];
        $convertedEncodingCount = 0;
        foreach ($outputs as $output) {
            $item = new \stdClass();
            foreach ($output->item as $field => $value) {
                $item->$field = $value;
            }
            $convItem['item'] = $item;
            $convItem['source'] = $output->source;
            $convItem['detected_encoding'] = $output->detected_encoding;
            $convItem['itemType'] = $output->itemType->name;
            $convItem['label'] = $output->label;
            $convItem['warnings'] = [];
            $convItem['notices'] = [];
            $convItem['scholarTitle'] = $this->makeScholarTitle($item->title);

            $convertedItems[$output->id] = $convItem;

            if ($output->detected_encoding != 'UTF-8') {
                $convertedEncodingCount++;
            }
        }

        $itemTypes = ItemType::all();
        $itemTypeOptions = $itemTypes->pluck('name', 'id')->all();

        $fileExists = Storage::disk('public')->exists('files/' . Auth::id() . '-' . $conversion->user_file_id . '-source.txt');

        return view('showConversion', compact('convertedItems', 'conversion', 'itemTypes', 'itemTypeOptions', 'fileExists', 'convertedEncodingCount', 'redirected'));
    }

    public function downloadSource(int $userFileId)
    {
        $userFile = UserFile::find($userFileId);

        if (!$userFile || $userFile->user_id != Auth::user()->id) {
            abort(403);
        }

        return Storage::download('public/files/' . $userFile->user_id . '-' . $userFileId . '-source.txt');
    }
}
