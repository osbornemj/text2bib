<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\ErrorReportComment;
use App\Models\RequiredResponse;
use Livewire\Component;

class RequireResponse extends Component
{
    public ErrorReportComment|Comment $comment;

    public int $userId;

    public string $type;

    public function submit(int $userId, int $commentId)
    {
        if ($this->type == 'comment') {
            RequiredResponse::create(['user_id' => $userId, 'comment_id' => $commentId]);
        } elseif ($this->type == 'errorReport') {
            RequiredResponse::create(['user_id' => $userId, 'error_report_comment_id' => $commentId]);
        }
    }

    public function remove(int $id)
    {
        RequiredResponse::destroy($id);
    }
}
