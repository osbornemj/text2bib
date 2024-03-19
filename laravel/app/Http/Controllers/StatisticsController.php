<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        $data = Statistic::orderBy('stat_date')->get();
        $labels = $data->pluck('stat_date')->toArray();
        $userCounts = $data->pluck('user_count')->toArray();
        $conversionCounts = $data->pluck('conversion_count')->toArray();
        $itemCounts = $data->pluck('item_count')->toArray();

        $colors = [
            "barPercentage" => "1.0",
            'backgroundColor' => "rgba(40, 100, 255, 0.4)",
            'borderColor' => "rgba(0, 0, 255, 0.7)",
            "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
            "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
            "pointHoverBackgroundColor" => "#fff",
            "pointHoverBorderColor" => "rgba(220,220,220,1)",
        ];

        $chartjsUsers = app()->chartjs
        ->name('userCounts')
        ->type('bar')
        ->size(['width' => 400, 'height' => 200])
        ->labels($labels)
        ->datasets([$colors +
            [
                "label" => "Number of users who converted at least one file",
                'data' => $userCounts,
            ],
        ])
        ->options([]);

        $chartjsConversions = app()->chartjs
        ->name('conversionCounts')
        ->type('bar')
        ->size(['width' => 400, 'height' => 200])
        ->labels($labels)
        ->datasets([$colors +
            [
                "label" => "Total number of conversions",
                'data' => $conversionCounts,
            ],
        ])
        ->options([]);

        $chartjsItems = app()->chartjs
        ->name('itemCounts')
        ->type('bar')
        ->size(['width' => 400, 'height' => 200])
        ->labels($labels)
        ->datasets([$colors +
            [
                "label" => "Total number of items converted",
                'data' => $itemCounts,
            ],
        ])
        ->options([]);

        return view('statistics', compact('chartjsUsers', 'chartjsConversions', 'chartjsItems'));
    }
}
