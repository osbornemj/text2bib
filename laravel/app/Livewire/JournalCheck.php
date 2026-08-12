<?php

namespace App\Livewire;

use App\Models\Journal;
use Livewire\Component;

class JournalCheck extends Component
{
    public Journal $journal;

    public string $checked;

    public string $currentPage;

    public string $type;

    public function check(string $value, string $currentPage)
    {
        $this->journal->checked = $value;
        $this->journal->save();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/journals?page='.$currentPage);
        } else {
            return redirect()->to('/admin/uncheckedJournals?page='.$currentPage);
        }
    }

    public function distinctive(string $value)
    {
        $this->journal->distinctive = $value;
        $this->journal->save();
    }

    public function delete(string $currentPage)
    {
        $this->journal->delete();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/journals?page='.$currentPage);
        } else {
            return redirect()->to('/admin/uncheckedJournals?page='.$currentPage);
        }
    }
}
