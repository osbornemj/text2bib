<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;

use App\Models\ErrorReportComment;

use Livewire\Component;

class ErrorFeedback extends Component
{
    public $comment;
    public $comments;
    public $errorReportId;

    public function mount()
    {
        $this->comments = ErrorReportComment::where('error_report_id', $this->errorReportId)->orderBy('created_at', 'asc')->get();
    }

    public function submit($errorReportId)
    {
        $user = Auth::user();
        ErrorReportComment::create([
            'error_report_id' => $errorReportId,
            'user_id' => $user->id,
            'comment_text' => $this->comment,
        ]);

        $this->comment ='';
        $this->comments = ErrorReportComment::where('error_report_id', $this->errorReportId)->orderBy('created_at', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.error-feedback');
    }
}
