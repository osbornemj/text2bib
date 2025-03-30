<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Bst;
use App\Models\Conversion;
use App\Models\ItemType;
use App\Models\Output;
use App\Models\Statistic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $userCount = User::count();
        $conversionCount = Conversion::count();
        $itemCount = Output::count();

        $data = Statistic::whereDate('stat_date', '>=', Carbon::now()->subDays(90)->toDateString())->orderBy('stat_date')->get();
        $labels = $data->pluck('stat_date')->toArray();
        $userCounts = $data->pluck('user_count')->toArray();
        $conversionCounts = $data->pluck('conversion_count')->toArray();
        $itemCounts = $data->pluck('item_count')->toArray();
        
        $monthlyData = Statistic::selectRaw(
            'year(stat_date) as year,
             month(stat_date) as month,
             sum(user_count) as user_counts, 
             sum(conversion_count) as conversion_counts, 
             sum(item_count) as item_counts'
             )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        $years = $monthlyData->pluck('year')->toArray();
        $months = $monthlyData->pluck('month')->toArray();
        foreach ($years as $i => $year) {
            $monthlyLabels[] = $year . '.' . $months[$i];
        }

        // select `use`,count(*) AS number from conversions where `use` is not null group by `use`;
        $useCounts = DB::table('conversions')
            ->whereNotNull('use')
            ->where('use', '!=', '')
            ->groupBy('use')
            ->select(DB::raw('`use`, count(*) AS use_count'))
            ->orderByDesc('use_count')
            ->get();

        $sourceCounts = DB::table('users')
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->groupBy('source')
            ->select(DB::raw('`source`, count(*) AS source_count'))
            ->orderByDesc('source_count')
            ->get();

        $itemTypeCounts = DB::table('outputs')
            ->join('item_types', 'item_types.id', '=', 'outputs.item_type_id')
            ->groupBy('item_types.name')
            ->select(DB::raw('item_types.name, count(*) as item_type_count'))
            ->orderByDesc('item_type_count')
            ->get();
        
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

        $chartjsUsersByMonth = app()->chartjs
        ->name('userCountsByMonth')
        ->type('bar')
        ->size(['width' => 400, 'height' => 200])
        ->labels($monthlyLabels)
        ->datasets([$colors +
            [
                "label" => "Sum over the days in each month of number of users who converted at least one file on that day",
                'data' => $monthlyData->pluck('user_counts')->toArray(),
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

        $chartjsConversionsByMonth = app()->chartjs
        ->name('conversionCountsByMonth')
        ->type('bar')
        ->size(['width' => 400, 'height' => 200])
        ->labels($monthlyLabels)
        ->datasets([$colors +
            [
                "label" => "Total number of conversions each month",
                'data' => $monthlyData->pluck('conversion_counts')->toArray(),
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

        $chartjsItemsByMonth = app()->chartjs
        ->name('itemCountsByMonth')
        ->type('bar')
        ->size(['width' => 400, 'height' => 200])
        ->labels($monthlyLabels)
        ->datasets([$colors +
            [
                "label" => "Total number of items converted",
                'data' => $monthlyData->pluck('item_counts')->toArray(),
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
 
          $chartjsSources = app()->chartjs
          ->name('sourceCounts')
          ->type('bar')
          ->size(['width' => 400, 'height' => 200])
          ->labels($sourceCounts->pluck('source')->toArray())
          ->datasets([$colors +
              [
                  "label" => "Number of users reporting each way of learning about the site",
                  'data' => $sourceCounts->pluck('source_count')->toArray(),
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
                'chartjsUsersByMonth', 
                'chartjsSources', 
                'chartjsConversions', 
                'chartjsConversionsByMonth', 
                'chartjsItems', 
                'chartjsItemsByMonth', 
                'chartjsUses',
                'userCount',
                'conversionCount',
                'itemCount',
                'itemTypeCounts',
            )
        );
    }

    public function bibtex(): View
    {
        $bsts = Bst::join('conversions', 'bsts.id', '=', 'conversions.bst_id')
            ->select('bsts.name', DB::raw('COUNT(DISTINCT conversions.user_id) as user_count'))
            ->where('bsts.available', 1)
            ->groupBy('bsts.id', 'bsts.name')
            ->orderByDesc('user_count')
            ->get();

        return view('statsBibtex', compact('bsts'));
    }

    public function languages(): View
    {
        $data = Conversion::select('language', DB::raw('COUNT(DISTINCT user_id) as user_count'))
            ->groupBy('language')
            ->orderByDesc('user_count')
            ->get();

        return view('statsLanguages', compact('data'));
    }

    public function crossref(): View
    {
        $data = Conversion::select('use_crossref', DB::raw('use_crossref, COUNT(*) as crossref_count'))
            ->groupBy('use_crossref')
            ->get();

        return view('statsCrossref', compact('data'));
    }
}
