<?php

namespace App\Http\Controllers;

use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\Statistic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $conversionCount = Conversion::count();
        $itemCount = Output::count();

        $data = Statistic::orderBy('stat_date')->get();
        $labels = $data->pluck('stat_date')->toArray();
        $userCounts = $data->pluck('user_count')->toArray();
        $conversionCounts = $data->pluck('conversion_count')->toArray();
        $itemCounts = $data->pluck('item_count')->toArray();
        // select `use`,count(*) AS number from conversions where `use` is not null group by `use`;
        $useCounts = DB::table('conversions')
            ->whereNotNull('use')
            ->where('use', '!=', '')
            ->groupBy('use')
            ->select(DB::raw('`use`, count(*) AS use_count'))
            ->orderByDesc('use_count')
            ->get();
        /*
        $itemTypeCounts = DB::table('outputs')
            ->join('item_types', 'item_types.id', '=', 'outputs.item_type_id')
            ->groupBy('item_type_id')
            ->select(DB::raw('item_types.name, count(*) as item_type_count'))
            ->orderByDesc('item_type_count')
            ->get();
        */

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
        ->options([
            "scales" => [
                "y" => [
                    "beginAtZero" => true
                    ]
                ]
         ]);

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
        ->options([
            "scales" => [
                "y" => [
                    "beginAtZero" => true
                    ]
                ]
         ]);

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
        ->options([
            "scales" => [
                "y" => [
                    "beginAtZero" => true
                    ]
                ]
         ]);

         $chartjsUses = app()->chartjs
         ->name('useCounts')
         ->type('bar')
         ->size(['width' => 400, 'height' => 200])
         ->labels($useCounts->pluck('use')->toArray())
         ->datasets([$colors +
             [
                 "label" => "Number of conversions for each intended use",
                 'data' => $useCounts->pluck('use_count')->toArray(),
             ],
         ])
         ->options([
             "scales" => [
                 "y" => [
                     "beginAtZero" => true
                     ]
                 ]
          ]);
 
         return view(
            'statistics', 
            compact(
                'chartjsUsers', 
                'chartjsConversions', 
                'chartjsItems', 
                'chartjsUses',
                'userCount',
                'conversionCount',
                'itemCount',
//                'itemTypeCounts',
            )
        );
    }
}
