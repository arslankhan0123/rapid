<?php

namespace App\Queries;

use App\Models\ManualSale;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * Class TagDataTable
 */
class ManulSalesDataTable
{
    /**
     * @param  array  $input
     * @return ManualSale
     */
    public function get($input = [])
    {
        /** @var ManualSale $query */
        // Add 'project' to the with() method
        $query = ManualSale::with(['customer', 'branch', 'project'])->orderBy('created_at', 'desc');

        $query->when(
            !empty($input['filterBranch']),
            function ($q) use ($input) {
                // Filter by the specific branch if provided
                $q->where('branch_id', $input['filterBranch']);
            },
            function ($q) {
                // Otherwise, filter by the user's associated branches
                $q->whereHas('branch', function ($branchQuery) {
                    $branchQuery->whereIn('id', function ($subQuery) {
                        $subQuery->select('branch_id')
                            ->from('users_branches')
                            ->where('user_id', auth()->id());
                    });
                });
            }
        );

        // Add month filter using invoice_date (FIXED)
        $query->when(!empty($input['filterMonth']), function ($q) use ($input) {
            $selectedDate = Carbon::createFromFormat('Y-m', $input['filterMonth']);
            $startDate = $selectedDate->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $selectedDate->copy()->endOfMonth()->format('Y-m-d');

            $q->whereBetween('invoice_date', [$startDate, $endDate]);
        });

        // Add customer filter
        $query->when(!empty($input['filterCustomer']), function ($q) use ($input) {
            $q->where('customer_id', $input['filterCustomer']);
        });

        // Add payment status filter
        $query->when(isset($input['paymentStatus']) && $input['paymentStatus'] !== '', function ($q) use ($input) {
            $q->where('payment_status', $input['paymentStatus']);
        });

        return $query;
    }
    // public function get($input = [])
    // {
    //     /** @var ManualSale $query */
    //     $query = ManualSale::with(['customer', 'branch'])->orderBy('created_at', 'desc');

    //     $query->when(
    //         !empty($input['filterBranch']),
    //         function ($q) use ($input) {
    //             // Filter by the specific branch if provided
    //             $q->where('branch_id', $input['filterBranch']);
    //         },
    //         function ($q) {
    //             // Otherwise, filter by the user's associated branches
    //             $q->whereHas('branch', function ($branchQuery) {
    //                 $branchQuery->whereIn('id', function ($subQuery) {
    //                     $subQuery->select('branch_id')
    //                         ->from('users_branches')
    //                         ->where('user_id', auth()->id());
    //                 });
    //             });
    //         }
    //     );

    //     // Add month filter using invoice_date (FIXED)
    //     $query->when(!empty($input['filterMonth']), function ($q) use ($input) {
    //         $selectedDate = Carbon::createFromFormat('Y-m', $input['filterMonth']);
    //         $startDate = $selectedDate->copy()->startOfMonth()->format('Y-m-d');
    //         $endDate = $selectedDate->copy()->endOfMonth()->format('Y-m-d');

    //         $q->whereBetween('invoice_date', [$startDate, $endDate]);
    //     });

    //     // Add customer filter
    //     $query->when(!empty($input['filterCustomer']), function ($q) use ($input) {
    //         $q->where('customer_id', $input['filterCustomer']);
    //     });

    //     // Add payment status filter
    //     $query->when(isset($input['paymentStatus']) && $input['paymentStatus'] !== '', function ($q) use ($input) {
    //         $q->where('payment_status', $input['paymentStatus']);
    //     });

    //     return $query;
    // }
}
