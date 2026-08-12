<?php

namespace App\Livewire;

use App\Enums\ReportStatus;
use App\Models\ErrorReport;
use Livewire\Component;

class ErrorReportStatus extends Component
{
    public array $statusOptions;

    public ReportStatus $status;

    public ErrorReport $errorReport;

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
