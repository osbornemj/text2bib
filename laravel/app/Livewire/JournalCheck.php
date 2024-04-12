<?php

namespace App\Livewire;

use Livewire\Component;

class JournalCheck extends Component
{
    public $journal;
    public $checked;

    public function check($value)
    {
        $checked = $this->journal->checked;
        $this->journal->checked = $value;
        $this->journal->save();

        if ($checked == 1) {
            return redirect()->to('/admin/journals');
        } else {
            return redirect()->to('/admin/uncheckedJournals');
        }
    }

    public function distinctive($value)
    {
        $this->journal->distinctive = $value;
        $this->journal->save();
    }

    public function delete()
    {
        $checked = $this->journal->checked;
        $this->journal->delete();

        if ($checked == 1) {
            return redirect()->to('/admin/journals');
        } else {
            return redirect()->to('/admin/uncheckedJournals');
        }
    }

}
