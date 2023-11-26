<?php

namespace App\Enums;

enum ReportStatus: int {
    case Open = 1;
    case Waiting = 2;
    case Closed = 5;
}