<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Conversion;

class Rating extends Component
{
    public $rating;
    public $conversionId;
    public $conversion;

    public function mount() {
        $this->conversion = Conversion::find($this->conversionId);
        $this->rating = 0;
    }

    public function setRating($val)
    {
        if ($this->rating == $val) {
            $this->rating = 0;
        } else {
            $this->rating = $val;
        }

        $this->conversion->update(['rating' => $this->rating]);
    }
}
