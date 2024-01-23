<?php

namespace App\Livewire;

use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\UserFile;
use App\Models\UserSetting;

use App\Traits\AddLabels;

use Livewire\Component;

use App\Livewire\Forms\ConvertFileForm;

use App\Services\Converter;

class ConvertFile extends Component
{
    use WithFileUploads;

    use AddLabels;

    public ConvertFileForm $form;

    private Converter $converter;

    public function boot()
    {
        $this->converter = new Converter();
    }

    public function mount()
    {
        $userSettings = UserSetting::where('user_id', Auth::id())->first();

        $defaults = [
            'item_separator' => 'line',
            'first_component' => 'authors',
            'label_style' => 'short',
            'override_labels' => '1',
            'line_endings' => 'w',
            'char_encoding' => 'utf8',
            'percent_comment' => '1',
            'include_source' => '1',
            'report_type' => 'standard',
            'save_settings' => '1',
        ];

        foreach ($defaults as $setting => $default) {
            $this->form->{$setting} = $userSettings ? $userSettings->{$setting} : $default;
        }
    }

    public function submit()
    {
        $this->validate();

        $file = $this->form->file;

        // write file to user_files table
        $sourceFile = new UserFile;
        $sourceFile->user_id = Auth::id();
        $sourceFile->file_type = $file->getClientMimeType();
        $sourceFile->file_size = $file->getSize();
        $sourceFile->original_filename = $file->getClientOriginalName();
        $sourceFile->type = 'SRC';
        $sourceFile->save();

        $file->storeAs(
            'files',
            Auth::id() . '-' . $sourceFile->id . '-source.txt',
            'public',
        );

        $settingValues = $this->form->toArray();
        unset($settingValues['file']);
        unset($settingValues['save_settings']);

        if ($this->form->save_settings) {
            $userSetting = UserSetting::firstOrNew( 
                ['user_id' => Auth::id()]
            );
            $userSetting->fill($settingValues);
            $userSetting->save();
        }

        $settingValues['user_file_id'] = $sourceFile->id;

        $conversion = new Conversion;
        $conversion->fill($settingValues);
        $conversion->user_id = Auth::id();
        $conversion->save();

        // Variant of convert method of ConversionController (with some adaptations for Livewire).
        // (Using Livewire so that loading indicator can be displayed easily.)
        // Is there a way to avoid the duplication?
        // Get file that user uploaded
        $filestring = Storage::disk('public')->get('files/' . Auth::id() . '-' . $conversion->user_file_id . '-source.txt');

        // Regularlize line-endings
        $filestring = str_replace(["\r\n", "\r"], "\n", $filestring);

        $entries = explode($conversion->item_separator == 'line' ? "\n\n" : "\n", $filestring);

        if (count($entries) == 1 && strlen($entries[0]) > 500) {
            $entry = $entries[0];
            return redirect('itemSeparatorError/' . $conversion->id)->with(['entry' => $entry]);
        }

        // Check for utf-8
        $nonUtf8Entries = [];
        foreach ($entries as $entry) {
            if (!mb_check_encoding($entry)) {
                $nonUtf8Entries[] = $entry;
            }
        }

        if (count($nonUtf8Entries)) {
            return redirect('encodingError/' . $conversion->id)->with(['nonUtf8Entries' => $nonUtf8Entries]);
        }

        $convertedEntries = [];
        foreach ($entries as $entry) {
            // $convertedEntries is array with components 'source', 'item', 'itemType', 'label', 'warnings',
            // 'notices', 'details', 'scholarTitle'.
            // 'label' (which depends on whole set of converted items) is updated later
            $convertedEntry = $this->converter->convertEntry($entry, $conversion);
            if ($convertedEntry) {
                $convertedEntries[] = $convertedEntry;
            }
        }

        $convertedEntries = $this->addLabels($convertedEntries, $conversion);

        $itemTypes = ItemType::all();

        // Write converted items to database **and key array to output ids**
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

        return redirect('showBibtex/' . $conversion->id)
            ->with([
                'convertedItems' => $convertedItems,
                'itemTypes' => $itemTypes,
                'conversion' => $conversion,
            ]);
    }
}
