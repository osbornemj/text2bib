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

    public function conversions(): View
    {
        $user = Auth::user();
        $conversions = $user->conversions->loadCount('outputs')->sortByDesc('created_at')->paginate(50);

        return view('conversions', compact('conversions'));
    }

    public function showConversion(int $conversionId): View
    {
        $conversion = Conversion::find($conversionId);

        if (!$conversion || $conversion->user_id != Auth::user()->id) {
            abort(403);
        }

        $outputs = Output::where('conversion_id', $conversionId)
                    ->with('itemType')
                    ->orderBy('seq')
                    ->get();

        $convertedItems = [];
        foreach ($outputs as $output) {
            $item = new \stdClass();
            foreach ($output->item as $field => $value) {
                $item->$field = $value;
            }
            $convItem['item'] = $item;
            $convItem['source'] = $output->source;
            $convItem['itemType'] = $output->itemType->name;
            $convItem['label'] = $output->label;
            $convItem['warnings'] = [];
            $convItem['notices'] = [];
            $convItem['scholarTitle'] = $this->makeScholarTitle($item->title);

            $convertedItems[$output->id] = $convItem;
        }

        $itemTypes = ItemType::all();
        $itemTypeOptions = $itemTypes->pluck('name', 'id')->all();

        return view('showConversion', compact('convertedItems', 'conversion', 'itemTypes', 'itemTypeOptions'));
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
