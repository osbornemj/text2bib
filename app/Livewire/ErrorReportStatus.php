<?php

namespace App\Livewire;

use Livewire\Component;

use App\Enums\ReportStatus;

use App\Models\ErrorReport;

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
        $this->errorReport->update(['status' => $this->status]);
    }

    public function render()
    {
        return view('livewire.error-report-status');
    }
}
