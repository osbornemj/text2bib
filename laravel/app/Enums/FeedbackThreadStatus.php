<?php

namespace App\Enums;

enum FeedbackThreadStatus: int {
    case Open = 1;
    case Waiting = 2;
    case Closed = 5;

    public function color(): string
    {
        return match($this) 
        {
            self::Open => 'text-green-600',   
            self::Waiting => 'text-red-600',   
            self::Closed => 'text-white-500',   
        };
    }    
}