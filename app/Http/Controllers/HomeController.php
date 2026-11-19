<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isStaff()) {
            $orders = Order::with('user')
                ->withCount('items')
                ->orderByDesc('order_date')
                ->orderByDesc('order_id')
                ->get();

            return view('home', compact('orders'));
        }

        $orders = Order::with(['items.stockItem.product.manufacturer', 'items.stockItem.size'])
            ->where('user_id', $user->user_id)
            ->orderByDesc('order_date')
            ->orderByDesc('order_id')
            ->get();

        $orders->each(function ($order) {
            $order->totalSum = (float) $order->items->sum(function ($item) {
                return $item->quantity * (float) $item->unit_price;
            });
        });

        return view('home', compact('orders'));
    }
}
