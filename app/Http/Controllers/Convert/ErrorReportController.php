<?php

namespace App\Http\Controllers\Convert;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ErrorReport;

class ErrorReportController extends Controller
{
    public function index()
    {
        $errorReports = ErrorReport::with('output')
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('errorReports', compact('errorReports'));
    }

    public function show($id)
    {
        $errorReport = ErrorReport::where('id', $id)
            ->with('output')
            ->with('output.itemType')
            ->with('output.rawOutput')
            ->with('output.rawOutput.itemType')
            ->first();

        return view('errorReport', compact('errorReport'));
    }
}
