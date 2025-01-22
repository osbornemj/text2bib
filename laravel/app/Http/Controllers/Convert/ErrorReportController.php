<?php

namespace App\Http\Controllers\Convert;

use App\Http\Controllers\Controller;
use App\Models\Conversion;
use App\Models\ErrorReport;
use App\Models\ErrorReportComment;
use Illuminate\View\View;

use App\Services\Converter;

class ErrorReportController extends Controller
{
    private Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
    }

    public function index(string $sortBy = 'status'): View
    {
        $errorReports = ErrorReport::with('output');

        if ($sortBy == 'latest') {
            $errorReports = $errorReports->orderByDesc('updated_at');
        } elseif ($sortBy == 'poster') {
            $errorReports = ErrorReport::join('outputs', 'outputs.id', '=', 'error_reports.output_id')
                ->join('conversions', 'conversions.id', '=', 'outputs.conversion_id')
                ->join('users', 'users.id', '=', 'conversions.user_id')
                ->orderBy('users.last_name')
                ->orderBy('users.first_name');
        } elseif ($sortBy == 'status') {
            $errorReports = ErrorReport::orderBy('status')->orderByDesc('updated_at');
        }

        $errorReports = $errorReports->paginate(50);

        return view('errorReports', compact('errorReports', 'sortBy'));
    }

    public function show($id): View
    {
        $errorReport = ErrorReport::where('id', $id)
            ->with('output')
            ->with('output.itemType')
            ->first();

        $opUser = $errorReport->output->conversion->user;

        return view('errorReport', compact('errorReport', 'opUser'));
    }

    public function convertSource(int $id)
    {
        $errorReport = ErrorReport::where('id', $id)
            ->with('output')
            ->with('output.itemType')
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
