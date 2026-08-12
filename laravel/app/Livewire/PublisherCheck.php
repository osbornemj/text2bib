<?php

namespace App\Livewire;

use App\Models\Publisher;
use Livewire\Component;

class PublisherCheck extends Component
{
    public Publisher $publisher;

    public string $currentPage;

    public string $type;

    public function check(string $value, string $currentPage)
    {
        $this->publisher->checked = $value;
        $this->publisher->save();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/publishers?page='.$currentPage);
        } else {
            return redirect()->to('/admin/uncheckedPublishers?page='.$currentPage);
        }
    }

    public function distinctive(string $value)
    {
        $this->publisher->distinctive = $value;
        $this->publisher->save();
    }

    public function delete(string $currentPage)
    {
        $this->publisher->delete();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/publishers?page='.$currentPage);
        } else {
            return redirect()->to('/admin/uncheckedPublishers?page='.$currentPage);
        }
    }
}
