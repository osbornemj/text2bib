<?php

namespace App\Livewire;

use Livewire\Component;

class JournalWordAbbreviationCheck extends Component
{
    public $journalWordAbbreviation;
    public $type;

    public function check($value, $type)
    {
        $this->journalWordAbbreviation->checked = $value;
        $this->journalWordAbbreviation->save();

        return redirect()->to('/admin/' . ($type == 'unchecked' ? 'uncheckedJ' : 'j') . 'ournalWordAbbreviations');
    }

    public function distinctive($value)
    {
        $this->journalWordAbbreviation->distinctive = $value;
        $this->journalWordAbbreviation->save();
    }

    public function delete($type)
    {
        $this->journalWordAbbreviation->delete();
        return redirect()->to('/admin/' . ($type == 'unchecked' ? 'uncheckedJ' : 'j') . 'ournalWordAbbreviations');
    }

}
