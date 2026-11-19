<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function previousMonthRange(Carbon $date)
    {
        $start = $date->copy()->subMonthNoOverflow()->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return [$start, $end];
    }

    protected function calculateCatalogPrice(Product $product, $hadPreviousMonthOrder)
    {
        return $hadPreviousMonthOrder
            ? (float) $product->price
            : round((float) $product->price * 0.75, 2);
    }

    protected function hadPreviousMonthOrder(Product $product)
    {
        [$start, $end] = $this->previousMonthRange(Carbon::today());

        return $product->stockItems()
            ->whereHas('orderItems.order', function ($query) use ($start, $end) {
                $query->whereBetween('order_date', [
                    $start->toDateString(),
                    $end->toDateString(),
                ]);
            })
            ->exists();
    }
}
