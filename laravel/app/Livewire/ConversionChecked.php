<?php

namespace App\Livewire;

use App\Models\AdminSetting;
use Livewire\Component;

class ConversionChecked extends Component
{
    public $maxCheckedConversionId;

    public $conversion;

    public function setMaxChecked()
    {
        $this->maxCheckedConversionId = $this->conversion->id;

        AdminSetting::find(1)->update(['max_checked_conversion_id' => $this->conversion->id]);
    }
}
