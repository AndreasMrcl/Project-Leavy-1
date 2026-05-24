<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->error('credentials', 'Email atau password salah.', 401);
        }

        $store = $user->store;
        if (! $store) {
            return $this->error('store', 'User belum memiliki store.', 403);
        }
        if ($store->status !== 'Settlement') {
            return $this->error('store', 'Store belum aktif.', 403);
        }

        $token = $user->createToken($data['device_name'])->plainTextToken;

        return $this->ok([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'level' => $user->level,
            ],
            'store' => $this->storePayload($store),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->noContent();
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return $this->ok([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'level' => $user->level,
            ],
            'store' => $this->storePayload($user->store),
        ]);
    }

    public function devices(Request $request)
    {
        $currentId = $request->user()->currentAccessToken()->id;

        $devices = $request->user()->tokens()->orderByDesc('last_used_at')->get()
            ->map(fn ($t) => [
                'id'           => $t->id,
                'name'         => $t->name,
                'last_used_at' => optional($t->last_used_at)->toIso8601String(),
                'created_at'   => $t->created_at->toIso8601String(),
                'current'      => $t->id === $currentId,
            ]);

        return $this->ok(['devices' => $devices]);
    }

    public function revokeDevice(Request $request, int $id)
    {
        $deleted = $request->user()->tokens()->where('id', $id)->delete();

        if (! $deleted) {
            return $this->error('device', 'Device tidak ditemukan.', 404);
        }

        return $this->noContent();
    }

    private function storePayload($store): array
    {
        $config = $store->storeConfig;

        return [
            'id'       => $store->id,
            'name'     => $store->store ?? $store->name,
            'location' => $store->location,
            'phone'    => $store->no_telpon,
            'status'   => $store->status,
            'config'   => $config ? [
                'currency'         => $config->currency,
                'tax_percent'      => $config->tax_percent,
                'tax_active'       => $config->tax_active,
                'service_percent'  => $config->service_percent,
                'service_active'   => $config->service_active,
                'receipt_header'   => $config->receipt_header,
                'receipt_footer'   => $config->receipt_footer,
                'min_stock_alert'  => $config->min_stock_alert,
            ] : null,
        ];
    }
}
