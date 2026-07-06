<?php

namespace App\Queries;


use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PurchaseItem;
use Carbon\Carbon;

/**
 * Class TagDataTable
 */
class PurchaseItemHistoryDataTable
{

    public function get($input = [])
    {
        /** @var PurchaseItem $query */
        $query = PurchaseItem::query()->with(['group', 'category', 'subCategory', 'orderItems', 'refundItems']);

        // Map the column index to actual database column names
        $columns = ['code', 'barcode', 'name', 'description', 'updated_at']; // Adjust this based on your displayed columns

        // Check if the 'order' input is provided for dynamic sorting
        if (isset($input['order'][0]['column'])) {
            $columnIndex = $input['order'][0]['column']; // Get the column index for sorting
            $direction = $input['order'][0]['dir'] ?? 'desc'; // Default to 'desc' if no direction is provided

            // Apply the order based on the requested column index, fallback to 'updated_at' if index is out of bounds
            $orderByColumn = $columns[$columnIndex] ?? 'updated_at';
            $query->orderBy($orderByColumn, $direction);
        } else {
            // If no 'order' input is provided, default sorting by 'updated_at'
            $query->orderBy('updated_at', 'desc');
        }
        if (!empty($input['start_date'])) {
            $start = Carbon::parse($input['start_date'])->startOfDay();
            $query->where(function ($q) use ($start) {
                $q->whereHas('orderItems', function ($q1) use ($start) {
                    $q1->where('created_at', '>=', $start);
                })->orWhereHas('refundItems', function ($q2) use ($start) {
                    $q2->where('created_at', '>=', $start);
                });
            });
        }

        if (!empty($input['end_date'])) {
            $end = Carbon::parse($input['end_date'])->endOfDay();

            $query->where(function ($q) use ($end) {
                $q->whereHas('orderItems', function ($q1) use ($end) {
                    $q1->where('created_at', '<=', $end);
                })->orWhereHas('refundItems', function ($q2) use ($end) {
                    $q2->where('created_at', '<=', $end);
                });
            });
        }

        if (!empty($input['seachItem'])) {
            $query->where(function ($q) use ($input) {
                $q->where('name', 'like', '%' . $input['seachItem'] . '%')
                    ->orWhere('code', 'like', '%' . $input['seachItem'] . '%')
                    ->orWhere('barcode', 'like', '%' . $input['seachItem'] . '%');
            });
        }

        // Fetch results
        $items = $query->get();

        // Append calculated subtotals
        $items->transform(function ($item) {
            $item->order_items_subtotal = $item->orderItems->sum(function ($i) {
                $subtotal = (float) $i->subtotal;
                $discount = (float) $i->discount;
                return $subtotal - $discount;
            });

            $item->refund_items_subtotal = $item->refundItems->sum(function ($r) {
                $subtotal = (float) $r->subtotal;
                $discount = (float) $r->discount;
                return $subtotal - $discount;
            });

            $item->total_sales_qty = $item->orderItems->sum(function ($i) {
                return (float) $i->qty;
            });

            $item->total_refund_qty = $item->refundItems->sum(function ($r) {
                return (float) $r->qty;
            });

            return $item;
        });

        return $items;
    }
}
