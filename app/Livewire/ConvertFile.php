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

use Livewire\Component;

use App\Livewire\Forms\ConvertFileForm;

use App\Services\Converter;

class ConvertFile extends Component
{
    use WithFileUploads;

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

//        $this->redirect('convert/' . $conversion->id);

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
            // 'notices', 'details'.
            // 'label' (which depends on whole set of converted items) is updated later
            $convertedEntry = $this->converter->convertEntry($entry, $conversion);
            if ($convertedEntry) {
                $convertedEntries[] = $convertedEntry;
            }
        }

        $convertedEntries = $this->addLabels($convertedEntries, $conversion);

        $itemTypes = ItemType::all();

        // Write converted items to database
        //$convertedItems = [];
        foreach ($convertedEntries as $i => $convItem) {
            Output::create([
                'source' => $convItem['source'],
                'conversion_id' => $conversion->id,
                'item_type_id' => $itemTypes->where('name', $convItem['itemType'])->first()->id,
                'label' => $convItem['label'],
                'item' => $convItem['item'],
                'warnings' => $convItem['warnings'],
                'notices' => $convItem['notices'],
                'details' => $convItem['details'],
                'scholar_title' => $convItem['scholarTitle'],
                'seq' => $i,
            ]);
        }

        return redirect('showBibtex/' . $conversion->id)
            ->with([
                'convertedEntries' => $convertedEntries,
                'itemTypes' => $itemTypes,
                'conversion' => $conversion,
            ]);
    }

    public function addLabels(array $convertedItems, Conversion $conversion): array
    {
        $baseLabels = [];
        foreach ($convertedItems as $key => $convertedItem) {
            if ($convertedItem['label'] && !$conversion->override_labels) {
                $baseLabel = $convertedItem['label'];
            } else {
                $baseLabel = $this->makeLabel($convertedItem['item'], $conversion);
            }

            $label = $baseLabel;
            // if $baseLabel already used, add a suffix to it
            if (in_array($baseLabel, $baseLabels)) {
                $values = array_count_values($baseLabels);
                $label .= chr(96 + $values[$baseLabel]);
            }

            $baseLabels[] = $baseLabel;
            $convertedItems[$key]['label'] = $label;
        }

        return $convertedItems;
    }

    public function makeLabel(object $item, Conversion $conversion): string
    {
        $label = '';
        if (isset($item->author) && $item->author) {
            $authors = explode(" and ", $item->author);
        } elseif (isset($item->editor)) {
            $authors = explode(" and ", $item->editor);
        } else {
            $authors = [];
        }
        if (isset($authors) && $authors) {
            foreach ($authors as $author) {
                $authorLetters = $this->onlyLetters(Str::ascii($author));
                if ($pos = strpos($author, ',')) {
                    if ($conversion->label_style == 'short') {
                        $label .= $authorLetters[0];
                    } else {
                        $label .= substr($authorLetters, 0, $pos);
                    }
                } else {
                    if ($conversion->label_style == 'short') {
                        $label .= substr(trim(strrchr($authorLetters, ' '), ' '), 0, 1);
                    } else {
                        $label .= trim(strrchr($authorLetters, ' '), ' ');
                    }
                }
            }
        }

        if ($conversion->label_style == 'short') {
            $label = mb_strtolower($label) . substr($item->year, 2);
        } elseif ($conversion->label_style == 'gs') {
            $firstAuthor = count($authors) ? $authors[0] : 'none';

            if (strpos($firstAuthor, ',') === false) {
                // assume last name is last segment
                if (strpos($firstAuthor, ' ') === false) {
                    $label = mb_strtolower($firstAuthor);
                } else {
                    $r = strrpos($firstAuthor, ' ');
                    $label = mb_strtolower(substr($firstAuthor, $r+1));
                }
            } else {
                // last name is segment up to comma
                $label = mb_strtolower(substr($firstAuthor, 0, strpos($firstAuthor, ',')));
            }
            $label .= $item->year;
            $title = $item->title;
            if (Str::startsWith($title, ['A ', 'The ', 'On ', 'An '])) {
                $title = Str::after($title, ' ');   
            }
            
            $firstTitleWord = Str::before($title, ' ');

            $label .= mb_strtolower($this->onlyLetters($firstTitleWord));
        } else {
            $label .= $item->year;
        }

        return $label;
    }

    // Returns string consisting only of letters and spaces in $string
    public function onlyLetters(string $string): string
    {
        return preg_replace("/[^a-z\s]+/i", "", $string);
    }

    public function render()
    {
        return view('livewire.convert-file');
    }
}
