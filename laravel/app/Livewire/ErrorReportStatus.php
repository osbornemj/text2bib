<?php

namespace App\Livewire;

use App\Enums\ReportStatus;
use Livewire\Component;

class ErrorReportStatus extends Component
{
    public $statusOptions;

    public $status;

    public $errorReport;

    public function mount()
    {
        $this->status = $this->errorReport->status;
        $this->statusOptions = collect(ReportStatus::cases())->pluck('name', 'value')->all();
    }

    public function updatedStatus()
    {
        // Don't change updated_at
        $this->errorReport->timestamps = false;
        $this->errorReport->update(['status' => $this->status]);
    }
}
