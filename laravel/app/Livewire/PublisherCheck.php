<?php

namespace App\Livewire;

use Livewire\Component;

class PublisherCheck extends Component
{
    public $publisher;
    public $currentPage;
    public $type;

    public function check($value, $currentPage)
    {
        $this->publisher->checked = $value;
        $this->publisher->save();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/publishers?page=' . $currentPage);
        } else {
            return redirect()->to('/admin/uncheckedPublishers?page=' . $currentPage);
        }
    }

    public function distinctive($value)
    {
        $this->publisher->distinctive = $value;
        $this->publisher->save();
    }

    public function delete($currentPage)
    {
        $this->publisher->delete();

        if ($this->type == 'checked') {
            return redirect()->to('/admin/publishers?page=' . $currentPage);
        } else {
            return redirect()->to('/admin/uncheckedPublishers?page=' . $currentPage);
        }
    }

}
