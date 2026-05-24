<?php

namespace App\Http\Controllers\Api\V1;

use App\Exports\OrderExport;
use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\HistoryResource;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $storeId = $request->user()->store->id;
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min(100, $perPage));

        $paginator = History::where('store_id', $storeId)
            ->latest()
            ->paginate($perPage);

        return $this->ok(
            HistoryResource::collection($paginator->items()),
            [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ]
        );
    }

    public function show(Request $request, int $id)
    {
        $storeId = $request->user()->store->id;

        $history = History::where('store_id', $storeId)->where('id', $id)->first();

        if (! $history) {
            return $this->error('history', 'History tidak ditemukan.', 404);
        }

        return $this->ok(['history' => new HistoryResource($history)]);
    }

    public function export(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|integer|between:1,12',
        ]);

        $storeId = $request->user()->store->id;

        $history = History::where('store_id', $storeId)
            ->whereMonth('created_at', $data['month'])
            ->get();

        $filename = 'history_'.$data['month'].'_'.Str::random(8).'.xlsx';
        $path = 'exports/'.$filename;

        Excel::store(new OrderExport($history, $data['month']), $path, 'public');

        $url = URL::temporarySignedRoute(
            'api.history.export.download',
            now()->addMinutes(10),
            ['file' => $filename]
        );

        return $this->ok([
            'url'        => $url,
            'expires_in' => 600,
            'filename'   => $filename,
        ]);
    }

    public function download(Request $request, string $file)
    {
        $path = 'exports/'.basename($file);
        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download($path);
    }
}
