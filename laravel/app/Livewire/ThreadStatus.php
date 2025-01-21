<?php

namespace App\Livewire;

use Livewire\Component;

use App\Enums\FeedbackThreadStatus;

class ThreadStatus extends Component
{
    public $statusOptions;
    public $status;
    public $thread;

    public function mount()
    {
        $this->status = $this->thread->status;
        $this->statusOptions = collect(FeedbackThreadStatus::cases())->pluck('name', 'value')->all();
    }

    public function updatedStatus()
    {
        // Don't change updated_at
        $this->thread->timestamps = false;
        $this->thread->update(['status' => $this->status]);
    }
}
