<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display role-tailored CMS admin dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Superadmin Dashboard Data
        if ($user->hasRole('superadmin')) {
            $stats = [
                'total_users'     => User::count(),
                'total_articles'  => Article::count(),
                'total_products'  => Product::count(),
                'total_revenue'   => Order::where('status', 'paid')
                                      ->orWhere('status', 'processing')
                                      ->orWhere('status', 'shipped')
                                      ->orWhere('status', 'delivered')
                                      ->orWhere('status', 'completed')
                                      ->sum('total'),
            ];

            $recentOrders = Order::with('user')->latest()->take(5)->get();
            $recentArticles = Article::with('author', 'category')->latest()->take(5)->get();
            
            // Sales Chart over past 7 days
            $chartData = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
                ->whereIn('status', ['paid', 'processing', 'shipped', 'delivered', 'completed'])
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return view('cms.dashboard.superadmin', compact('stats', 'recentOrders', 'recentArticles', 'chartData'));
        }

        // 2. Author Dashboard Data
        if ($user->hasRole('author')) {
            $stats = [
                'my_articles'       => Article::where('user_id', $user->id)->count(),
                'published_articles'=> Article::where('user_id', $user->id)->published()->count(),
                'draft_articles'    => Article::where('user_id', $user->id)->where('status', 'draft')->count(),
                'total_views'       => Article::where('user_id', $user->id)->sum('view_count'),
            ];

            $myRecentArticles = Article::where('user_id', $user->id)
                ->with('category')
                ->latest()
                ->take(5)
                ->get();

            return view('cms.dashboard.author', compact('stats', 'myRecentArticles'));
        }

        // 3. Marketplace Admin Dashboard Data
        if ($user->hasRole('admin-marketplace')) {
            $stats = [
                'total_sales'     => Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered', 'completed'])->count(),
                'total_products'  => Product::count(),
                'low_stock'       => Product::where('track_stock', true)->where('stock', '<=', 5)->count(),
                'total_revenue'   => Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered', 'completed'])->sum('total'),
            ];

            $recentOrders = Order::with('user')->latest()->take(10)->get();
            $lowStockProducts = Product::where('track_stock', true)->where('stock', '<=', 5)->latest()->take(5)->get();

            return view('cms.dashboard.marketplace', compact('stats', 'recentOrders', 'lowStockProducts'));
        }

        abort(403, 'Akses ditolak.');
    }
}
