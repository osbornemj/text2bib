<?php

namespace App\Http\Controllers\Convert;

use App\Http\Controllers\Controller;
use App\Models\Conversion;
use App\Models\ErrorReport;
use Illuminate\View\View;

use App\Services\Converter;

class ErrorReportController extends Controller
{
    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    public function index(): View
    {
        $errorReports = ErrorReport::with('output')
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('errorReports', compact('errorReports'));
    }

    public function show($id): View
    {
        $errorReport = ErrorReport::where('id', $id)
            ->with('output')
            ->with('output.itemType')
            ->with('output.rawOutput')
            ->with('output.rawOutput.itemType')
            ->first();

        return view('errorReport', compact('errorReport'));
    }

    public function convertSource(int $id)
    {
        $errorReport = ErrorReport::where('id', $id)
            ->with('output')
            ->with('output.itemType')
            ->with('output.rawOutput')
            ->with('output.rawOutput.itemType')
            ->first();

        $conversion = new Conversion;
        $conversion->item_separator = 'line';
        $conversion->char_encoding = 'utf8';
        $conversion->percent_comment = true;
        $conversion->include_source = true;
        $conversion->report_type = 'detailed';

        $result = $this->converter->convertEntry($errorReport->output->source, $conversion);

        return view('errorReportConversion', compact('result'));
    }
}
