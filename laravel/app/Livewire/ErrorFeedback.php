<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;

use App\Models\ErrorReportComment;

use Livewire\Component;
use Livewire\Attributes\Rule;

class ErrorFeedback extends Component
{
    #[Rule('required', message: 'Please enter a comment')]    
    public $comment;

    public $comments;
    public $errorReportId;

    public function mount()
    {
        $this->comments = ErrorReportComment::where('error_report_id', $this->errorReportId)
            ->orderBy('created_at')
            ->get();
    }

    public function submit($errorReportId)
    {
        $this->validate();
        
        $user = Auth::user();
        $comment = ErrorReportComment::create([
            'error_report_id' => $errorReportId,
            'user_id' => $user->id,
            'comment_text' => $this->comment,
        ]);

        $this->comment = '';
        $this->comments = $this->comments->push($comment);
    }
}
