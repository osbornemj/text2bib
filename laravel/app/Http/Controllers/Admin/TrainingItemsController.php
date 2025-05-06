<?php

namespace App\Http\Controllers\Admin;

use DB;
use Throwable;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\View\View;

use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\TrainingItem;
use App\Models\VonName;

use App\Services\Converter;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrainingItemsController extends Controller
{
    public function index(): View
    {
        $trainingItems = TrainingItem::with('output.conversion')->orderBy('id')->paginate(50);

        $type = 'all';

        return view('admin.trainingItems.index', compact('trainingItems', 'type'));
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
    public function copy(): RedirectResponse
    {
        $maxCheckedConversionId = AdminSetting::first()->max_checked_conversion_id;

        DB::statement('DELETE FROM training_items');
        DB::statement('ALTER TABLE training_items AUTO_INCREMENT 1');
        DB::statement('INSERT INTO training_items (output_id)
            SELECT o.id
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
            WHERE c.usable = 1 AND c.id <= ' . $maxCheckedConversionId);

        //DB::statement('UPDATE training_items SET language="fr" WHERE source LIKE "%Décem%" COLLATE utf8mb4_bin AND language="en"');

        return back();
    }

    public function showLowercase(): View
    {
        $vonNames = VonName::all();

        $trainingItems = TrainingItem::whereHas('output', function ($q) use ($vonNames) {
            $q->whereRaw('BINARY source regexp "^[a-z]"');
            foreach ($vonNames as $vonName) {
                $q = $q->where('source', 'not like', $vonName->name . ' %');
            }
            $q = $q->where('source', 'not like', 'd\'%');
        })->get();

        $type = 'lowercase';

        $trainingItems = $trainingItems->paginate(50);

        return view('admin.trainingItems.index', compact('trainingItems', 'type'));
    }

    public function convert(): RedirectResponse
    {
        // Allow script to run for up to 180 minutes (10800 seconds)
        set_time_limit(10800);

        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $converter = new Converter;

        $itemTypes = ItemType::select('id', 'name')->get();
        foreach ($itemTypes as $itemType) {
            $itemTypeIds[$itemType->name] = $itemType->id;
        }

        AdminSetting::first()->update([
            'training_items_conversion_count' => 0, 
            'training_items_conversion_started_at' => now(),
            'training_items_conversion_ended_at' => null,
        ]);

        $conversion = new Conversion;
        $chunkSize = 5000;
        TrainingItem::select('id', 'output_id')->with('output.conversion')
            ->chunkById($chunkSize, function (Collection $trainingItems) use ($conversion, $itemTypeIds, $converter) {
                $itemsAndTypes = [];
                $conversionCount = AdminSetting::first()->training_items_conversion_count;
                foreach ($trainingItems as $trainingItem) {
                    $result = $converter->convertEntry(
                        $trainingItem->output->source, 
                        $conversion, 
                        $trainingItem->output->conversion->language, 
                        'utf8leave', 
                        'biblatex'
                    );
                    if (!$result) {
                        $trainingItem->delete();
                    } else {
                        $itemsAndType = [];
                        try {
                            $itemsAndType['item'] = json_encode($result['item']);
                        } catch (Throwable $e) {
                            report($e);
                            $trainingItem->delete();
                        }
                        if ($trainingItem) {
                            //$itemsAndType['id'] = $trainingItem->id;
                            $itemsAndType['id'] = $trainingItem->output_id;
                            $itemsAndType['source'] = $trainingItem->output->source;
                            $itemsAndType['conversion_id'] = $trainingItem->output->conversion_id;
                            $itemsAndType['label'] = $trainingItem->output->label;
                            $itemsAndType['seq'] = $trainingItem->output->seq;
                            $itemsAndType['item_type_id'] = $itemTypeIds[$result['itemType']];
                            $itemsAndTypes[] = $itemsAndType;
                            $conversionCount++;
                        }
                    }
                }
                // Even though source, conversion_id, label, and seq fields are not being updated, apparently need to specify
                // them because upsert might do an insert if an entry does not exist (although in this case the entries always exist).
                Output::upsert($itemsAndTypes, ['id'], ['item', 'item_type_id']);
                AdminSetting::first()->update(['training_items_conversion_count' => $conversionCount]);
            });

            AdminSetting::first()->update(['training_items_conversion_ended_at' => now()]);

            return back();
    }

    public function selectAndFormat()
    {
        $trainingItems = TrainingItem::inRandomOrder()->limit(6000)->get();

        $itemTypes = ItemType::select('id', 'name')->get();
        foreach ($itemTypes as $itemType) {
            $itemTypeNames[$itemType->id] = $itemType->name;
        }

        $data = [];
        foreach ($trainingItems as $trainingItem) {
            $output = $trainingItem->output;
            $i = $output->id;
            $data[$i] = [];
            $data[$i]['input'] = $output->source;
            $data[$i]['output'] = [];
            $data[$i]['output']['type'] = $itemTypeNames[$output->item_type_id];
            $item = $output->item;
            foreach ($item as $name => $field) {
                $data[$i]['output'][$name] = $field;
            }
        }
 
        return new StreamedResponse(
            function () use ($data) {
                $handle = fopen('php://output', 'w');
                fwrite($handle, json_encode($data));
                fclose($handle);
            },
            200,
            [
                'Content-type'        => 'text/plain; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename=training_data.json'
            ]
        );
    }
}
