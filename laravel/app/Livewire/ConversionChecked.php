<?php

namespace App\Livewire;

use App\Models\AdminSetting;
use App\Models\Conversion;
use Livewire\Component;

class ConversionChecked extends Component
{
    public int $maxCheckedConversionId;

    public Conversion $conversion;

    public function setMaxChecked()
    {
        $this->maxCheckedConversionId = $this->conversion->id;

        AdminSetting::find(1)->update(['max_checked_conversion_id' => $this->conversion->id]);
    }
}
