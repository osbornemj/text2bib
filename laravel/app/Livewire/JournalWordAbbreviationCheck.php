<?php

namespace App\Livewire;

use Livewire\Component;

class JournalWordAbbreviationCheck extends Component
{
    public $journalWordAbbreviation;
    public $type;

    public function check($value)
    {
        $this->journalWordAbbreviation->checked = $value;
        $this->journalWordAbbreviation->save();

        return redirect()->to('/admin/journalWordAbbreviations');
    }

    public function distinctive($value)
    {
        $this->journalWordAbbreviation->distinctive = $value;
        $this->journalWordAbbreviation->save();
    }

    public function delete()
    {
        $this->journalWordAbbreviation->delete();
        return redirect()->to('/admin/journalWordAbbreviations');
    }

}
