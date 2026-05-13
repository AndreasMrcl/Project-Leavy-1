<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Chair;
use App\Models\Discount;
use App\Models\History;
use App\Models\Invent;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Showcase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Pagescontroller extends Controller
{
    // public function dashboard(Request $request)
    // {

    //     // CARDS
    //     $total_order = Order::count();
    //     $total_product = Menu::count();
    //     $total_users = User::where('level', 'user')->count();
    //     $top_seller = History::selectRaw("SUBSTRING_INDEX(`order`, ' - ', 1) AS product_name")
    //         ->groupBy('order')
    //         ->orderByRaw('COUNT(*) DESC')
    //         ->limit(1)
    //         ->pluck('product_name')
    //         ->first();

    //     // CHARTS ORDER
    //     $orders = History::selectRaw("COUNT(*) as count, DATE_FORMAT(created_at, '%M') as month_name, MONTH(created_at) as month_number")
    //         ->whereYear('created_at', date('Y'))
    //         ->groupBy('month_number', 'month_name')
    //         ->orderBy('month_number')
    //         ->pluck('count', 'month_name');

    //     $labels1 = $orders->keys();
    //     $data1 = $orders->values();

    //     // CHARTS REVENUE
    //     $revenue = History::selectRaw("SUM(total_amount) as revenue, DATE_FORMAT(created_at, '%M') as month_name, MONTH(created_at) as month_number")
    //         ->whereYear('created_at', date('Y'))
    //         ->groupBy('month_number', 'month_name')
    //         ->orderBy('month_number')
    //         ->pluck('revenue', 'month_name');

    //     $labels2 = $revenue->keys();
    //     $data2 = $revenue->values();

    //     // CHARTS SETTLEMENT
    //     $settlements = Settlement::selectRaw('DATE(start_time) as date, SUM(total_amount) as total')
    //         ->groupBy('date')
    //         ->get();

    //     $labels3 = $settlements->pluck('date')->toArray(); // Convert to array
    //     $data3 = $settlements->pluck('total')->toArray(); // Ensure this matches the sum total key

    //     // CHARTS EXPENSE
    //     $expense = Expense::selectRaw("SUM(nominal) as expense, DATE_FORMAT(created_at, '%M') as month_name, MONTH(created_at) as month_number")
    //         ->whereYear('created_at', date('Y'))
    //         ->groupBy('month_number', 'month_name')
    //         ->orderBy('month_number')
    //         ->pluck('expense', 'month_name');

    //     $labels4 = $expense->keys();
    //     $data4 = $expense->values();

    //     // Variables related to filtering (optional: if you're using year/month filters)
    //     $selectedYear = $request->input('selectedYear', date('Y'));
    //     $selectedDate = $request->input('selectedDate', date('m'));

    //     // You can define $dataSets if needed (e.g., for multi-data charts)
    //     $dataSets = [
    //         'orders' => $data1,
    //         'revenue' => $data2,
    //         'settlements' => $data3,
    //         'expenses' => $data4,
    //     ];

    //     return view('dashboard', [
    //         'total_order' => $total_order,
    //         'total_product' => $total_product,
    //         'total_users' => $total_users,
    //         'top_seller' => $top_seller,
    //         'labels1' => $labels1,
    //         'data1' => $data1,
    //         'labels2' => $labels2,
    //         'data2' => $data2,
    //         'labels3' => $labels3,
    //         'data3' => $data3,
    //         'labels4' => $labels4,
    //         'data4' => $data4,
    //         'dataSets' => $dataSets,
    //         'selectedYear' => $selectedYear,
    //         'selectedDate' => $selectedDate
    //     ]);
    // }

    public function dashboard()
    {
        $user = Auth::user();
        $storeId = $user->store->id;

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $weekStart = Carbon::today()->subDays(6);
        $monthStart = Carbon::now()->startOfMonth();

        // KPI: Pemasukan & jumlah order hari ini (active settled + history)
        $todayActiveOrders = Order::where('store_id', $storeId)
            ->where('status', 'settlement')
            ->whereDate('created_at', $today)
            ->with('cart:id,total_amount')
            ->get();

        $todayHistories = History::where('store_id', $storeId)
            ->whereDate('created_at', $today)
            ->get(['total_amount']);

        $todayRevenue = $todayActiveOrders->sum(fn ($o) => $o->cart->total_amount ?? 0)
            + $todayHistories->sum('total_amount');

        $todayOrderCount = $todayActiveOrders->count() + $todayHistories->count();

        // Yesterday — untuk hitung tren
        $yesterdayActiveOrders = Order::where('store_id', $storeId)
            ->where('status', 'settlement')
            ->whereDate('created_at', $yesterday)
            ->with('cart:id,total_amount')
            ->get();
        $yesterdayHistories = History::where('store_id', $storeId)
            ->whereDate('created_at', $yesterday)
            ->get(['total_amount']);
        $yesterdayRevenue = $yesterdayActiveOrders->sum(fn ($o) => $o->cart->total_amount ?? 0)
            + $yesterdayHistories->sum('total_amount');
        $yesterdayOrderCount = $yesterdayActiveOrders->count() + $yesterdayHistories->count();

        $revenueTrend = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : null;
        $orderTrend = $yesterdayOrderCount > 0
            ? round((($todayOrderCount - $yesterdayOrderCount) / $yesterdayOrderCount) * 100, 1)
            : null;

        // KPI: Order aktif (semua yang belum di-archive — orders table)
        $activeOrderCount = Order::where('store_id', $storeId)->count();

        // Monthly summary — untuk 4 colored cards
        $monthlyActiveOrders = Order::where('store_id', $storeId)
            ->where('status', 'settlement')
            ->where('created_at', '>=', $monthStart)
            ->with('cart:id,total_amount')
            ->get();
        $monthlyHistories = History::where('store_id', $storeId)
            ->where('created_at', '>=', $monthStart)
            ->get(['total_amount']);
        $monthlyRevenue = $monthlyActiveOrders->sum(fn ($o) => $o->cart->total_amount ?? 0)
            + $monthlyHistories->sum('total_amount');
        $monthlyOrderCount = $monthlyActiveOrders->count() + $monthlyHistories->count();

        $monthlyCustomers = Cart::where('store_id', $storeId)
            ->where('created_at', '>=', $monthStart)
            ->whereNotNull('chair_id')
            ->distinct('chair_id')
            ->count('chair_id');

        // KPI: Stok menipis
        $lowStock = Invent::where('store_id', $storeId)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderByRaw('(stock - min_stock) asc')
            ->take(5)
            ->get();
        $lowStockCount = Invent::where('store_id', $storeId)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        // Chart: 7 hari pemasukan
        $revenueByDate = [];
        for ($i = 0; $i < 7; $i++) {
            $revenueByDate[$weekStart->copy()->addDays($i)->format('Y-m-d')] = 0;
        }

        Order::where('store_id', $storeId)
            ->where('status', 'settlement')
            ->whereDate('created_at', '>=', $weekStart)
            ->with('cart:id,total_amount')
            ->get()
            ->each(function ($o) use (&$revenueByDate) {
                $key = $o->created_at->format('Y-m-d');
                if (isset($revenueByDate[$key])) {
                    $revenueByDate[$key] += $o->cart->total_amount ?? 0;
                }
            });

        History::where('store_id', $storeId)
            ->whereDate('created_at', '>=', $weekStart)
            ->get(['created_at', 'total_amount'])
            ->each(function ($h) use (&$revenueByDate) {
                $key = $h->created_at->format('Y-m-d');
                if (isset($revenueByDate[$key])) {
                    $revenueByDate[$key] += $h->total_amount;
                }
            });

        $chartLabels = collect(array_keys($revenueByDate))
            ->map(fn ($d) => Carbon::parse($d)->isoFormat('D MMM'))
            ->values()
            ->all();
        $chartData = array_values($revenueByDate);

        // Top 5 menu bulan ini (parse history.order: "name - qty - notes - name - qty - notes - ")
        $monthOrderStrings = History::where('store_id', $storeId)
            ->where('created_at', '>=', $monthStart)
            ->pluck('order');

        $sellerCounts = [];
        foreach ($monthOrderStrings as $orderStr) {
            if (! $orderStr) {
                continue;
            }
            $parts = explode(' - ', rtrim($orderStr, ' - '));
            for ($i = 0; $i < count($parts); $i += 3) {
                $name = trim($parts[$i] ?? '');
                $qty = (int) ($parts[$i + 1] ?? 0);
                if ($name !== '' && $qty > 0) {
                    $sellerCounts[$name] = ($sellerCounts[$name] ?? 0) + $qty;
                }
            }
        }
        arsort($sellerCounts);
        $topSellers = collect($sellerCounts)->take(5);
        $topSellerMax = $topSellers->max() ?: 1;

        // Order terbaru (5)
        $recentOrders = Order::where('store_id', $storeId)
            ->with(['cart.cartMenus'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todayRevenue',
            'todayOrderCount',
            'activeOrderCount',
            'lowStockCount',
            'lowStock',
            'revenueTrend',
            'orderTrend',
            'monthlyRevenue',
            'monthlyOrderCount',
            'monthlyCustomers',
            'chartLabels',
            'chartData',
            'topSellers',
            'topSellerMax',
            'recentOrders'
        ));
    }

    public function search(Request $request)
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        $userStore = Auth::user()->store;

        if (! $userStore) {
            return redirect()->route('addstore');
        }

        $search = $request->input('search');

        $menus = Menu::with('category')->where('store_id', $userStore->id);
        $categories = Category::where('store_id', $userStore->id);
        $invents = Invent::where('store_id', $userStore->id);
        $orders = Order::with(['cart'])->where('store_id', $userStore->id);
        $histories = History::where('store_id', $userStore->id);
        $discounts = Discount::where('store_id', $userStore->id);
        $showcases = Showcase::where('store_id', $userStore->id);
        $chairs = Chair::where('store_id', $userStore->id);

        if ($search) {

            $menus->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });

            $categories->where('name', 'LIKE', "%{$search}%");

            $invents->where('name', 'LIKE', "%{$search}%");

            $orders->where(function ($q) use ($search) {
                $q->where('atas_nama', 'LIKE', "%{$search}%")
                    ->orWhere('no_telpon', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%");
            });

            $histories->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('akun', 'LIKE', "%{$search}%")
                    ->orWhere('order', 'LIKE', "%{$search}%");
            });

            $discounts->where('name', 'LIKE', "%{$search}%");

            $showcases->where('name', 'LIKE', "%{$search}%");

            $chairs->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        return view('search', [
            'menus'      => $menus->get(),
            'categories' => $categories->get(),
            'invents'    => $invents->get(),
            'orders'     => $orders->get(),
            'histories'  => $histories->get(),
            'discounts'  => $discounts->get(),
            'showcases'  => $showcases->get(),
            'chairs'     => $chairs->get(),
        ]);
    }

    public function profile()
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        $userStore = Auth::user()->store;

        if (! $userStore) {
            return redirect()->route('addstore');
        }

        return view('profile', compact('userStore'));
    }
}