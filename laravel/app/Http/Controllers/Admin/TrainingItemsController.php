<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

use App\Models\Conversion;
use App\Models\TrainingItem;

use App\Services\Converter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Throwable;

class TrainingItemsController extends Controller
{
    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    public function index(): View
    {
        $trainingItems = TrainingItem::paginate(50);
        $languageOptions = config('constants.languages');

        return view('admin.trainingItems.index', compact('trainingItems', 'languageOptions'));
    }

    public function clean()
    {
        $trainingItems = TrainingItem::select('id', 'source')->get();

        foreach ($trainingItems as $trainingItem) {
            if (
                strlen($trainingItem->source) < 35 
                ||
                strlen($trainingItem->source) > 1000 
                ||
                substr_count($trainingItem->source, ' ') < 4
                ||
                preg_match('/^[_-]{2,}[.,]?/', $trainingItem->source)
               ) {
                $trainingItem->delete();
            }
        }

        return back();
    }

    public function convert()
    {
        $trainingItems = TrainingItem::select('id', 'source', 'language')->get();
        $conversion = new Conversion;
        foreach ($trainingItems as $trainingItem) {
            $output = $this->converter->convertEntry($trainingItem->source, $conversion, $trainingItem->language, 'utf8leave', 'biblatex', null);
            // If get JSON encoding error, delete $trainingItem
            try {
                $trainingItem->update(['item' => $output['item'], 'type' => $output['itemType']]);
            } catch (Throwable $e) {
                report($e);
                $trainingItem->delete();
            }
        }

        return back();
    }
}
