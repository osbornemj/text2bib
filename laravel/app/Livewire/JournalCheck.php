<?php

namespace App\Livewire;

use Livewire\Component;

class JournalCheck extends Component
{
    public $journal;
    public $checked;
    public $currentPage;
    public $type;

    public function check($value, $currentPage)
    {
        $this->journal->checked = $value;
        $this->journal->save();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/journals?page=' . $currentPage);
        } else {
            return redirect()->to('/admin/uncheckedJournals?page=' . $currentPage);
        }
    }

    public function distinctive($value)
    {
        $this->journal->distinctive = $value;
        $this->journal->save();
    }

    public function delete($currentPage)
    {
        $this->journal->delete();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/journals?page=' . $currentPage);
        } else {
            return redirect()->to('/admin/uncheckedJournals?page=' . $currentPage);
        }
    }

}
