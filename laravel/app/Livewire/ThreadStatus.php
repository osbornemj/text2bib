<?php

namespace App\Livewire;

use App\Enums\FeedbackThreadStatus;
use App\Models\Thread;
use Livewire\Component;

class ThreadStatus extends Component
{
    public array $statusOptions;

    public string $status;

    public Thread $thread;

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
