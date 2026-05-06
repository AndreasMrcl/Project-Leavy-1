<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    public function loginQr(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $qrToken = Str::random(40);

        $user->qr_token = $qrToken;

        $user->save();

        $baseUrl = env('FRONTEND_URL');

        $qrUrl = $baseUrl.'/signin?qrToken='.$qrToken.'&name='.urlencode($user->name);

        $qrCode = QrCode::size(400)->generate($qrUrl);

        $filename = 'qrcodes/'.Str::random(10).'.svg';

        Storage::disk('public')->put($filename, $qrCode);

        return view('qrcode', [
            'filename' => $filename,
            'user' => $user,
        ]);
    }
}
