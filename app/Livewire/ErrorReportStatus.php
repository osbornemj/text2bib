<?php

namespace App\Livewire;

use Livewire\Component;

use App\Enums\ReportStatus;

class ErrorReportStatus extends Component
{
    public $statusOptions;

    public function mount()
    {
        $this->statusOptions = array_column(ReportStatus::cases(), 'name');
    }

    public function setStatus()
    {
                
    }

    public function render()
    {
        return view('livewire.error-report-status');
    }
}
