<?php

namespace App\Livewire;

use Livewire\Component;

class StartJournalAbbreviationCheck extends Component
{
    public $startJournalAbbreviation;
    public $type;

    public function check($value)
    {
        $this->startJournalAbbreviation->checked = $value;
        $this->startJournalAbbreviation->save();

        return redirect()->to('/admin/startJournalAbbreviations');
    }

    public function distinctive($value)
    {
        $this->startJournalAbbreviation->distinctive = $value;
        $this->startJournalAbbreviation->save();
    }

    public function delete()
    {
        $this->startJournalAbbreviation->delete();
        return redirect()->to('/admin/startJournalAbbreviations');
    }

}
