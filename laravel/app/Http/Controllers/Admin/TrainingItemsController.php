<?php

namespace App\Http\Controllers\Admin;

use DB;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

use App\Models\Conversion;
use App\Models\TrainingItem;
use App\Models\VonName;
use App\Services\Converter;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * Delete all items from training_items and then copy the source field from outputs together with the
     * associated language field from conversions, restricting to cases in which
     * source has at least 35 characters
     * source has at most 1000 characters
     * source does not start with __ or -- or —
     * source has at least 4 spaces
     * In the case of duplicates, take the source for which the conversion_id is largest.
     * Could use REGEXP rather than LIKE, but probably is slower?:
     *    AND source NOT REGEXP "^[_-]{2,}"
     *    AND source NOT REGEXP "^—"
     */
    public function copy()
    {
        DB::statement('DELETE FROM training_items');
        DB::statement('ALTER TABLE training_items AUTO_INCREMENT 1');
        DB::statement('INSERT INTO training_items (source, language, conversion_id) 
            SELECT o.source, c.language, c.id
            FROM outputs o
            JOIN (
                SELECT source, MAX(conversion_id) AS max_conversion_id
                FROM outputs
                    WHERE LENGTH(source) >= 35 
                        AND LENGTH(source) <= 1000 
                        AND source NOT LIKE "\_\_%"
                        AND source NOT LIKE "--%"
                        AND source NOT LIKE "—%"
                        AND source LIKE "% % % % %"
                GROUP BY source
            ) latest ON o.source = latest.source AND o.conversion_id = latest.max_conversion_id
            JOIN conversions c ON o.conversion_id = c.id
            WHERE c.usable = 1');

        DB::statement('UPDATE training_items SET language="fr" WHERE source LIKE "%Décem%" COLLATE utf8mb4_bin AND language="en"');

        return back();
    }

    /**
     * Not used now --- the cleaning is done in the copy method.
     */
    public function clean()
    {
        TrainingItem::chunk(10000, function ($trainingItems) {
            foreach ($trainingItems as $trainingItem) {
                if (
                    strlen($trainingItem->source) < 35 
                    ||
                    strlen($trainingItem->source) > 1000 
                    ||
                    substr_count($trainingItem->source, ' ') < 4
                    ||
                    preg_match('/^[_-—]{2,}/', $trainingItem->source)
                   ) {
                    $trainingItem->delete();
                }
            }
        });

        return back();
    }

    public function showLowercase()
    {
        $vonNames = VonName::all();

        $trainingItems = TrainingItem::whereRaw('BINARY source regexp "^[a-z]"');

        foreach ($vonNames as $vonName) {
            $trainingItems = $trainingItems->where('source', 'not like', $vonName->name . ' %');
        }

        $trainingItems = $trainingItems->paginate(100);

        return view('admin.trainingItems.showLowercase', compact('trainingItems'));
    }

    public function convert()
    {
        // Allow script to run for up to 120 minutes (7200 seconds)
        set_time_limit(7200);

        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $conversion = new Conversion;
        TrainingItem::select('id', 'source', 'language', 'conversion_id')->whereNull('item')
            ->chunkById(5000, function (Collection $trainingItems) use ($conversion) {
                $itemsAndTypes = [];
                foreach ($trainingItems as $trainingItem) {
                    $output = $this->converter->convertEntry($trainingItem->source, $conversion, $trainingItem->language, 'utf8leave', 'biblatex', null);
                    if (!$output) {
                        $trainingItem->delete();
                    } else {
                        $itemsAndType = [];
                        try {
                            $itemsAndType['item'] = json_encode($output['item']);
                        } catch (Throwable $e) {
                            report($e);
                            $trainingItem->delete();
                        }
                        if ($trainingItem) {
                            $itemsAndType['id'] = $trainingItem->id;
                            $itemsAndType['conversion_id'] = $trainingItem->conversion_id;
                            $itemsAndType['source'] = $trainingItem->source;
                            $itemsAndType['type'] = $output['itemType'];
                            $itemsAndTypes[] = $itemsAndType;
                        }
                    }
                }
                // Even though source field is not being updated, apparently need to specify it because upsert
                // might do an insert if an entry does not exist (although in this case the entries always exist).
                TrainingItem::upsert($itemsAndTypes, ['id'], ['item', 'type', 'conversion_id']);
            });

        return back();
    }
}
