<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    public function index()
    {
        $history = Cache::remember(
            'history',
            now()->addMinutes(60),
            fn () => History::all()
        );

        return view('history', ['history' => $history]);
    }

    public function exportOrders(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
        ]);

        $month = $request->month;
        $history = History::whereMonth('created_at', $month)->get();

        return Excel::download(new OrderExport($history, $month), 'history_'.$month.'.xlsx');
    }
}
