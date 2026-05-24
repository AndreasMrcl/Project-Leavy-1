<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreConfigResource;
use Illuminate\Http\Request;

class StoreConfigController extends Controller
{
    use ApiResponse;

    public function show(Request $request)
    {
        $config = $request->user()->store->storeConfig;

        if (! $config) {
            return $this->ok(['store_config' => null]);
        }

        return $this->ok(['store_config' => new StoreConfigResource($config)]);
    }
}
