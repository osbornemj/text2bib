<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;

use App\Models\ErrorReport;
use App\Models\ErrorReportComment;
use App\Models\RequiredResponse;
use App\Models\User;
use App\Notifications\ErrorReportCommentPosted;
use Livewire\Component;
use Livewire\Attributes\Rule;

class ErrorFeedback extends Component
{
    #[Rule('required', message: 'Please enter a comment')]    
    public $comment;

    public $comments;
    public $errorReportId;
    public $opUser;
    public $type;
    public $userIsAdmin;

    public function mount()
    {
        $this->comments = ErrorReportComment::where('error_report_id', $this->errorReportId)
            ->orderBy('created_at')
            ->get();

        $this->userIsAdmin = Auth::user()->is_admin;
    }

    public function submit()
    {
        $this->validate();

        $errorReportCommentIds = ErrorReport::find($this->errorReportId)->errorReportComments->pluck('id');

        $user = Auth::user();
        $this->userIsAdmin = $user->is_admin;

        $comment = ErrorReportComment::create([
            'error_report_id' => $this->errorReportId,
            'user_id' => $user->id,
            'comment_text' => $this->comment,
        ]);

        $this->comment = '';
        $this->comments = $this->comments->push($comment);

        // Delete required response, if there is one
        RequiredResponse::where('user_id', $user->id)->whereIn('error_report_comment_id', $errorReportCommentIds)->first()?->delete();

        // Notify user who posted report (if different from user)
        $errorReport = ErrorReport::find($this->errorReportId);
        $reportUser = $errorReport->output->conversion->user;
        if ($reportUser->id != $user->id) {
            $reportUser->notify(new ErrorReportCommentPosted($errorReport, $reportUser));
        }

        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            if ($admin->id != $user->id) {
                $admin->notify(new ErrorReportCommentPosted($errorReport, $admin));
            }
        }
    }
}
