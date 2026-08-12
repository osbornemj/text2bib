<?php

namespace App\Livewire;

use App\Models\JournalWordAbbreviation;
use Livewire\Component;

class JournalWordAbbreviationCheck extends Component
{
    public JournalWordAbbreviation $journalWordAbbreviation;

    public string $type;

    public function check(string $value, string $type)
    {
        $this->journalWordAbbreviation->checked = $value;
        $this->journalWordAbbreviation->save();

        return redirect()->to('/admin/'.($type == 'unchecked' ? 'uncheckedJ' : 'j').'ournalWordAbbreviations');
    }

    public function distinctive(string $value)
    {
        $this->journalWordAbbreviation->distinctive = $value;
        $this->journalWordAbbreviation->save();
    }

    public function delete(string $type)
    {
        $this->journalWordAbbreviation->delete();

        return redirect()->to('/admin/'.($type == 'unchecked' ? 'uncheckedJ' : 'j').'ournalWordAbbreviations');
    }
}
