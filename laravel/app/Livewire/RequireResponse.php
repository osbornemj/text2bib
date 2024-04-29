<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\RequiredResponse;

class RequireResponse extends Component
{
    public $comment;
    public $userId;
    public $type;

    public function submit($userId, $commentId)
    {
        if ($this->type == 'comment') {
            RequiredResponse::create(['user_id' => $userId, 'comment_id' => $commentId]);
        } elseif ($this->type == 'errorReport') {
            RequiredResponse::create(['user_id' => $userId, 'error_report_comment_id' => $commentId]);
        }
    }

    public function remove($id)
    {
        RequiredResponse::destroy($id);
    }

}
