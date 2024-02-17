<?php

namespace App\Livewire;

use Livewire\Component;

class JournalCheck extends Component
{
    public $journal;

    public function check($value)
    {
        $this->journal->checked = $value;
        $this->journal->save();

        return redirect()->to('/admin/journals');
    }

    public function distinctive($value)
    {
        $this->journal->distinctive = $value;
        $this->journal->save();
    }

    public function delete()
    {
        $this->journal->delete();
        return redirect()->to('/admin/journals');
    }

}
