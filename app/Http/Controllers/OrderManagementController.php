<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderManagementController extends Controller
{
    private function loadOrderWithTotal(Order $order)
    {
        $order->load([
            'user',
            'items.stockItem.product.manufacturer',
            'items.stockItem.size',
        ]);

        $order->totalSum = (float) $order->items->sum(function ($item) {
            return $item->quantity * (float) $item->unit_price;
        });

        return $order;
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);
        $search = $validated['search'] ?? null;

        $products = Product::with(['manufacturer', 'category'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->get();

        $draft = $request->session()->get('order_draft', []);
        $draftItems = StockItem::with(['product.manufacturer', 'size'])
            ->whereIn('stock_item_id', array_keys($draft))
            ->get()
            ->map(function ($item) use ($draft) {
                $item->draftQuantity = (int) $draft[$item->stock_item_id];
                $item->lineTotal = $item->draftQuantity * (float) $item->product->price;
                return $item;
            });

        $draftTotal = (float) $draftItems->sum('lineTotal');
        $clients = User::where('role_id', User::ROLE_CLIENT)
            ->orderBy('last_name')->orderBy('first_name')->get();

        return view('orders.create', compact('products', 'search', 'draftItems', 'draftTotal', 'clients'));
    }

    public function selectProduct(Product $product)
    {
        $product->load(['manufacturer', 'category', 'stockItems.size']);
        return view('orders.select', compact('product'));
    }

    public function addDraftItem(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => ['required', 'integer', 'exists:stock_items,stock_item_id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $stockItem = StockItem::findOrFail($validated['stock_item_id']);
        if ($validated['quantity'] > $stockItem->available_quantity) {
            return back()->withInput()->with('error', 'Недостаточно товара на складе.');
        }

        $draft = $request->session()->get('order_draft', []);
        $draft[$stockItem->stock_item_id] = (int) $validated['quantity'];
        $request->session()->put('order_draft', $draft);

        return redirect()->route('home.orders.create')->with('success', 'Позиция добавлена в черновик заказа.');
    }

    public function removeDraftItem(Request $request, StockItem $stockItem)
    {
        $draft = $request->session()->get('order_draft', []);
        unset($draft[$stockItem->stock_item_id]);
        $request->session()->put('order_draft', $draft);

        return redirect()->route('home.orders.create')->with('success', 'Позиция удалена из черновика.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
            'order_date' => ['required', 'date'],
        ]);

        $draft = $request->session()->get('order_draft', []);
        if (empty($draft)) {
            return back()->with('error', 'Добавьте товары в заказ.');
        }

        DB::transaction(function () use ($validated, $draft) {
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'order_date' => $validated['order_date'],
            ]);

            foreach ($draft as $stockItemId => $quantity) {
                $stockItem = StockItem::with('product')->lockForUpdate()->findOrFail($stockItemId);
                if ($quantity > $stockItem->available_quantity) {
                    throw new \RuntimeException('Недостаточно товара на складе.');
                }

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'stock_item_id' => $stockItem->stock_item_id,
                    'quantity' => $quantity,
                    'unit_price' => $stockItem->product->price,
                ]);

                $stockItem->decrement('available_quantity', $quantity);
            }
        });

        $request->session()->forget('order_draft');

        return redirect()->route('home')->with('success', 'Заказ создан.');
    }

    public function show(Order $order)
    {
        $order = $this->loadOrderWithTotal($order);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order = $this->loadOrderWithTotal($order);
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_date' => ['required', 'date'],
        ]);

        $order->update(['order_date' => $validated['order_date']]);

        return redirect()->route('home.orders.edit', $order)
            ->with('success', 'Дата заказа обновлена.');
    }

    public function destroy(Order $order)
    {
        $orderId = $order->order_id;
        $order->items()->delete();
        $order->delete();

        return redirect()->route('home')->with('success', 'Заказ №' . $orderId . ' удалён.');
    }

    public function destroyItem(Order $order, OrderItem $item)
    {
        if ((int) $item->order_id !== (int) $order->order_id) {
            abort(404);
        }

        $item->delete();

        return redirect()->route('home.orders.edit', $order)
            ->with('success', 'Товарная позиция удалена из заказа.');
    }
}
