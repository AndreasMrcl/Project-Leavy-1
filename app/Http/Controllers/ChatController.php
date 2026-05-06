<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Prism;
use Throwable;

class ChatController extends Controller
{
    public function bot()
    {

        if (! Auth::check()) {
            return redirect('/');
        }

        $userStore = Auth::user()->store;

        if (! $userStore) {
            return redirect()->route('addstore');
        }

        $status = $userStore->status;

        if ($status !== 'Settlement') {
            return redirect()->route('login');
        }

        $chats = Chat::orderBy('created_at', 'asc')
            ->take(50)
            ->get();

        return view('bot', compact('chats'));
    }

    public function gen(Request $request)
    {
        $prompt = trim(strip_tags($request->input('prompt')));

        if (! $this->validatePrompt($prompt)) {
            return response()->json([
                'error' => 'Prompt tidak boleh kosong dan harus lebih dari 3 karakter.',
            ], 400);
        }

        if ($responseFromDatabase = $this->tryAnalyzePrompt($prompt)) {
            $this->saveChat($prompt, $responseFromDatabase);

            return response()->json(['response' => $responseFromDatabase], 200);
        }

        $fullPrompt = $this->buildConversationContext($prompt);

        return $this->generateResponse($prompt, $fullPrompt);
    }

    private function validatePrompt(string $prompt): bool
    {
        return $prompt && strlen($prompt) >= 3 && strlen($prompt) <= 500;
    }

    private function tryAnalyzePrompt(string $prompt): ?string
    {
        return $this->analyzePrompt($prompt);
    }

    private function buildConversationContext(string $prompt): string
    {
        $previousChats = Chat::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->reverse();

        $context = '';
        foreach ($previousChats as $chat) {
            $context .= "User: {$chat->prompt}\nBot: {$this->stripHtml($chat->response)}\n";
        }

        return $context."User: {$prompt}\nBot:";
    }

    private function generateResponse(string $prompt, string $fullPrompt)
    {
        try {
            $response = Prism::text()
                ->using(Provider::Gemini, 'gemini-2.0-flash')
                ->withPrompt($fullPrompt)
                ->asText();

            $generatedText = trim($response->text);
            $generatedHtml = (new CommonMarkConverter)->convert($generatedText)->getContent();

            $this->saveChat($prompt, $generatedHtml);

            return response()->json(['response' => $generatedHtml], 200);
        } catch (PrismException $e) {
            Log::error('Text generation failed', ['exception' => $e]);

            return response()->json(['error' => 'Gagal menghasilkan teks. Silakan coba lagi nanti.'], 500);
        } catch (Throwable $e) {
            Log::error('Generic error in ChatController', ['exception' => $e]);

            return response()->json(['error' => 'Terjadi kesalahan tak terduga. Silakan coba lagi nanti.'], 500);
        }
    }

    private function saveChat(string $prompt, string $response): void
    {
        Chat::create([
            'store_id' => Auth::user()->store->id,
            'prompt' => $prompt,
            'response' => $response,
        ]);
    }

    private function stripHtml(string $html): string
    {
        return trim(strip_tags($html));
    }

    private function analyzePrompt(string $prompt): ?string
    {
        $patterns = $this->getPatterns();
        $promptLower = strtolower($prompt);

        if (preg_match($patterns['sales_today'], $prompt)) {
            return $this->getTotalSalesToday();
        }

        if (preg_match($patterns['sales_month'], $prompt, $matches)) {
            return $this->getTotalSalesMonth($matches);
        }

        if (preg_match($patterns['orders_today'], $prompt)) {
            return $this->getOrdersToday();
        }

        if (preg_match($patterns['top_payment'], $prompt)) {
            return $this->getTopPayment();
        }

        if (preg_match($patterns['sales_week_trend'], $prompt)) {
            return $this->getSalesWeekTrend();
        }

        if (preg_match($patterns['best_selling_products'], $prompt)) {
            return $this->getBestSellingProducts();
        }

        if (preg_match($patterns['branch_sales'], $prompt, $matches)) {
            return $this->getBranchSales($matches[1]);
        }

        if (preg_match($patterns['average_sales_today'], $prompt)) {
            return $this->getAverageSalesToday();
        }

        if (preg_match($patterns['total_transactions_today'], $prompt)) {
            return $this->getTotalTransactionsToday();
        }

        return null;
    }

    private function getPatterns(): array
    {
        return [
            'sales_today' => '/(total )?(penjualan|pendapatan|omset) (hari ini|sekarang)/i',
            'sales_month' => '/(total )?(penjualan|pendapatan|omset) bulan (ini|[a-z]+ \d{4})/i',
            'orders_today' => '/jumlah (order|pesanan) (hari ini|sekarang)/i',
            'top_payment' => '/pembayaran (terbanyak|populer|paling sering)/i',
            'sales_week_trend' => '/tren (penjualan|pendapatan) minggu ini/i',
            'best_selling_products' => '/produk (terlaris|paling laris|favorit)/i',
            'branch_sales' => '/penjualan cabang (.+)/i',
            'average_sales_today' => '/rata-rata (penjualan|pendapatan) (per transaksi )?(hari ini|sekarang)/i',
            'total_transactions_today' => '/total transaksi (hari ini|sekarang)/i',
        ];
    }

    // ==================== METODE UNIVERSAL TANPA STORE ID ====================

    private function getTotalSalesToday(): string
    {
        $today = now()->toDateString();
        $total = History::where('status', 'settlement')
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        return 'Total penjualan hari ini adalah Rp '.number_format($total, 0, ',', '.');
    }

    private function getTotalSalesMonth(array $matches): string
    {
        $now = now();
        $monthString = strtolower(trim($matches[2] ?? ''));

        if (in_array($monthString, ['ini', 'bulan ini'])) {
            $year = $now->year;
            $month = $now->month;
        } else {
            try {
                $dt = \Carbon\Carbon::createFromFormat('F Y', ucfirst($monthString));
                $year = $dt->year;
                $month = $dt->month;
            } catch (\Exception $e) {
                $year = $now->year;
                $month = $now->month;
            }
        }

        $total = History::where('status', 'settlement')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('total_amount');

        $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return "Total penjualan bulan {$monthName} adalah Rp ".number_format($total, 0, ',', '.');
    }

    private function getOrdersToday(): string
    {
        $count = History::where('status', 'settlement')
            ->whereDate('created_at', now())
            ->count();

        return "Jumlah order hari ini adalah $count pesanan.";
    }

    private function getTopPayment(): string
    {
        $top = History::where('status', 'settlement')
            ->select('payment_type', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_type')
            ->orderByDesc('total')
            ->first();

        if (! $top) {
            return 'Belum ada data pembayaran.';
        }

        $description = match (strtolower($top->payment_type)) {
            'qris' => 'QRIS (scan QR code)',
            'cash' => 'tunai (cash)',
            'gopay' => 'GoPay (dompet digital)',
            'ovo' => 'OVO (dompet digital)',
            'dana' => 'Dana (dompet digital)',
            default => ucfirst($top->payment_type),
        };

        return "Jenis pembayaran terbanyak adalah {$description} sebanyak {$top->total} kali.";
    }

    private function getSalesWeekTrend(): string
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $sales = History::where('status', 'settlement')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($sales->isEmpty()) {
            return 'Belum ada penjualan minggu ini.';
        }

        $result = "Tren penjualan minggu ini:\n";
        foreach ($sales as $day) {
            $result .= date('D, d M', strtotime($day->date)).': Rp '.number_format($day->total, 0, ',', '.')."\n";
        }

        return $result;
    }

    private function getBestSellingProducts(): string
    {
        $productsRaw = History::where('status', 'settlement')->get(['order']);

        $productCounts = [];

        foreach ($productsRaw as $row) {
            $parts = explode(' - ', $row->order);
            $productName = $parts[0] ?? null;
            $qty = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : 1;

            if (! $productName) {
                continue;
            }

            if (! isset($productCounts[$productName])) {
                $productCounts[$productName] = 0;
            }
            $productCounts[$productName] += $qty;
        }

        if (empty($productCounts)) {
            return 'Belum ada data produk.';
        }

        arsort($productCounts);

        $result = "Produk terlaris (berdasarkan jumlah kuantitas terjual):\n";
        $topProducts = array_slice($productCounts, 0, 5, true);
        foreach ($topProducts as $name => $count) {
            $result .= "- $name: $count pcs terjual\n";
        }

        return $result;
    }

    private function getBranchSales(string $storeName): string
    {
        $total = History::whereRaw('LOWER(store_name) = ?', [strtolower($storeName)])
            ->where('status', 'settlement')
            ->whereDate('created_at', now()->toDateString())
            ->sum('total_amount');

        if ($total == 0) {
            return "Cabang \"$storeName\" tidak ditemukan atau belum ada transaksi hari ini.";
        }

        return "Total penjualan hari ini untuk cabang {$storeName}: Rp ".number_format($total, 0, ',', '.');
    }

    private function getAverageSalesToday(): string
    {
        $today = now()->toDateString();
        $total = History::where('status', 'settlement')
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $count = History::where('status', 'settlement')
            ->whereDate('created_at', $today)
            ->count();

        if ($count === 0) {
            return 'Belum ada transaksi hari ini.';
        }

        $average = $total / $count;

        return 'Rata-rata penjualan per transaksi hari ini adalah Rp '.number_format($average, 0, ',', '.');
    }

    private function getTotalTransactionsToday(): string
    {
        $count = History::where('status', 'settlement')
            ->whereDate('created_at', now())
            ->count();

        return "Total transaksi hari ini adalah $count transaksi.";
    }
}
