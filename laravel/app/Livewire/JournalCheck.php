<?php

namespace App\Livewire;

use Livewire\Component;

class JournalCheck extends Component
{
    public $journal;
    public $checked;
    public $currentPage;

    public function check($value, $currentPage)
    {
        $checked = $this->journal->checked;
        $this->journal->checked = $value;
        $this->journal->save();

        if ($checked == 1) {
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
        $checked = $this->journal->checked;
        $this->journal->delete();

        if ($checked == 1) {
            return redirect()->to('/admin/journals?page=' . $currentPage);
        } else {
            return redirect()->to('/admin/uncheckedJournals?page=' . $currentPage);
        }
    }

}
